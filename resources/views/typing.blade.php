<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Typing Game</title>
    @vite(['resources/css/app.css', 'resources/js/app.js']) {{-- Tailwind via Laravel Vite --}}
    <style>
        #display-text span {
            white-space: pre-wrap;
        }

        .highlight {
            background-color: green;
            animation: blink 1s infinite;
        }

        @keyframes blink {
            0%, 100% { background-color: green; }
            50% { background-color: transparent; }
        }
    </style>
</head>
<body class="bg-[#1e1e2f] text-[#f8f8f2] font-mono p-8 leading-relaxed">
    <header class="flex justify-center gap-6 mb-6 text-lg font-semibold">
        <a href="{{ route('play', 'easy') }}" class="text-green-300 hover:underline">Easy</a>
        <a href="{{ route('play', 'medium') }}" class="text-yellow-300 hover:underline">Medium</a>
        <a href="{{ route('play', 'hard') }}" class="text-orange-300 hover:underline">Hard</a>
        <a href="{{ route('play', 'hardcore') }}" class="text-red-400 hover:underline">Hardcore</a>
    </header>

    <div id="display-text" class="text-lg bg-[#2d2d44] p-4 rounded-xl shadow mb-6 min-h-[120px]">
        {{ $text->content }}
    </div>

    <input 
        id="input-box" 
        type="text" 
        name="hidden-typing-field"
        class="absolute opacity-0 pointer-events-none"
        autocomplete="off"
        autocorrect="off"
        autocapitalize="off"
        spellcheck="false"
        onpaste="return false;" 
        oncontextmenu="return false;" 
        oncopy="return false;" 
        oncut="return false;" 
        ondragstart="return false;" 
        ondrop="return false;"
        aria-hidden="true"
    />


    <div id="wpm" class="mb-1">WPM: 0</div>
    <div id="accuracy" class="mb-1">Accuracy: 100%</div>
    <div id="errors" class="mb-1">Errors: 0</div>
    <div id="timer" class="mb-4">Time: 00:00:000</div>

    <audio id="sound-correct" src="https://assets.mixkit.co/sfx/download/mixkit-positive-interface-beep-221.mp3" preload="auto"></audio>
    <audio id="sound-error" src="https://assets.mixkit.co/sfx/download/mixkit-wrong-answer-buzz-950.mp3" preload="auto"></audio>

    <div id="result-modal" class="hidden fixed top-[30%] left-1/2 transform -translate-x-1/2 -translate-y-[30%] bg-[#282846] text-white border-2 border-cyan-300 p-8 rounded-xl shadow-2xl z-50 text-center w-full max-w-md">
    <h2 class="text-xl font-bold">Finished!</h2>
    <p id="final-wpm" class="mt-2"></p>
    <p id="final-accuracy"></p>
    <p id="final-time"></p>
    <p id="final-badge" class="mt-4 font-semibold text-lg"></p>

    <div class="mt-6 flex flex-wrap justify-center gap-4">
        <!-- Play Again -->
        <button 
            onclick="location.reload()" 
            class="bg-green-400 text-black px-4 py-2 rounded hover:bg-green-300 transition"
        >
            Play Again
        </button>

        <!-- Submit Score -->
        <form method="POST" action="{{ route('submit') }}">
            @csrf
            <input type="hidden" name="difficulty" value="{{ $difficulty }}">
            <input type="hidden" id="completion_time" name="completion_time">
            <input type="hidden" id="wpm_input" name="WPM">
            <input type="hidden" id="accuracy_input" name="accuracy">
            
            <button 
                type="submit" 
                id="submit-btn" 
                class="bg-blue-400 text-black px-4 py-2 rounded hover:bg-blue-300 transition hidden"
            >
                Submit Score
            </button>
        </form>
    </div>
</div>




    <script>
        let startTime, timerInterval, started = false;
        let errorCount = 0;
        let errorTracker = {};

        const inputBox = document.getElementById('input-box');
        const originalText = document.getElementById('display-text').innerText;
        const correctSound = document.getElementById('sound-correct');
        const errorSound = document.getElementById('sound-error');

        window.onload = () => inputBox.focus();
        document.body.addEventListener('click', () => inputBox.focus());

        inputBox.addEventListener('keydown', (e) => {
            if ((e.ctrlKey || e.metaKey) && ['v', 'c', 'x', 'a'].includes(e.key.toLowerCase())) {
                e.preventDefault();
            }

            if (e.key === 'Backspace') {
                const index = inputBox.value.length;
                delete errorTracker[index]; // Let user retry that spot
            }
        });

        inputBox.addEventListener('input', () => {
            const typed = inputBox.value;

            if (!started) {
                started = true;
                startTime = Date.now();
                timerInterval = setInterval(() => {
                    const elapsed = Date.now() - startTime;
                    document.getElementById('timer').textContent = 'Time: ' + formatTime(elapsed);
                    updateWPM(elapsed);
                    updateAccuracy();
                }, 100);
            }

            if (typed.length >= originalText.length) {
                endGame();
            }

            let resultHTML = '';
            for (let i = 0; i < originalText.length; i++) {
                const origChar = originalText[i];
                const typedChar = typed[i];

                if (typedChar == null) {
                    resultHTML += i === typed.length
                        ? `<span class="highlight">${origChar}</span>`
                        : `<span>${origChar}</span>`;
                } else if (typedChar === origChar) {
                    resultHTML += `<span class="text-green-400">${origChar}</span>`;
                } else {
                    resultHTML += `<span class="text-red-400">${origChar}</span>`;
                    if (!errorTracker[i]) {
                        errorCount++;
                        errorTracker[i] = true;
                        errorSound.play();
                    }
                }
            }

            document.getElementById('display-text').innerHTML = resultHTML;

            if (typed.length > 0 && typed[typed.length - 1] === originalText[typed.length - 1]) {
                correctSound.play();
            }
        });

        function endGame() {
            clearInterval(timerInterval);
            const timeTaken = Date.now() - startTime;
            document.getElementById('completion_time').value = timeTaken;

            const wpmText = document.getElementById('wpm').textContent;
            const accuracyText = document.getElementById('accuracy').textContent;

            // Extract numbers
            const wpmValue = parseInt(wpmText.replace(/\D/g, ''));
            const accuracyValue = parseInt(accuracyText.replace(/\D/g, ''));

            // 🧠 Populate form fields before submit
            document.getElementById('wpm_input').value = wpmValue;
            document.getElementById('accuracy_input').value = accuracyValue;

            // Show the modal and submit button
            document.getElementById('submit-btn').style.display = 'block';

            // Display on modal
            document.getElementById('final-wpm').textContent = wpmText;
            document.getElementById('final-accuracy').textContent = accuracyText;
            document.getElementById('final-time').textContent = 'Time: ' + formatTime(timeTaken);

            // Badge logic
            let badgeText = '';
            if (wpmValue < 30) badgeText = '🐢 Beginner';
            else if (wpmValue < 60) badgeText = '🐇 Intermediate';
            else if (wpmValue < 100) badgeText = '🦅 Advanced';
            else badgeText = '🔥 Typing God';

            document.getElementById('final-badge').textContent = badgeText;
            document.getElementById('result-modal').classList.remove('hidden');
        }


        function formatTime(ms) {
            const minutes = String(Math.floor(ms / 60000)).padStart(2, '0');
            const seconds = String(Math.floor((ms % 60000) / 1000)).padStart(2, '0');
            const milliseconds = String(ms % 1000).padStart(3, '0');
            return `${minutes}:${seconds}:${milliseconds}`;
        }

        function updateWPM(elapsedMs) {
            const typed = inputBox.value;
            const correctChars = [...typed].filter((char, i) => char === originalText[i]).length;
            const wpm = Math.round((correctChars / 5) / (elapsedMs / 60000));
            document.getElementById('wpm').textContent = 'WPM: ' + (isNaN(wpm) ? 0 : wpm);
        }

        function updateAccuracy() {
            const typed = inputBox.value;
            const totalTyped = typed.length;
            const correctChars = [...typed].filter((char, i) => char === originalText[i]).length;
            const accuracy = totalTyped === 0 ? 100 : Math.round((correctChars / totalTyped) * 100);
            document.getElementById('accuracy').textContent = 'Accuracy: ' + accuracy + '%';
            document.getElementById('errors').textContent = 'Errors: ' + errorCount;
        }
    </script>
</body>
</html>
