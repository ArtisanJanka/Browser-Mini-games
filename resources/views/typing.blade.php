<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Typing Game</title>
    <style>
        body {
            font-family: 'Courier New', monospace;
            background: #1e1e2f;
            color: #f8f8f2;
            padding: 2rem;
            line-height: 1.6;
        }

        #display-text {
            font-size: 1.2rem;
            background: #2d2d44;
            padding: 1rem;
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(255,255,255,0.1);
            margin-bottom: 1rem;
            min-height: 120px;
        }

        #display-text span {
            white-space: pre-wrap;
        }

        /* Visually hide input */
        #input-box {
            position: absolute;
            opacity: 0;
            pointer-events: none;
        }

        #wpm, #timer, #accuracy, #errors {
            margin: 0.25rem 0;
        }

        .highlight {
            background-color: yellow;
            animation: blink 1s infinite;
        }

        @keyframes blink {
            0%, 100% { background-color: yellow; }
            50% { background-color: transparent; }
        }

        #result-modal {
            display: none;
            background: #282846;
            color: white;
            border: 2px solid #8be9fd;
            padding: 2rem;
            border-radius: 10px;
            text-align: center;
            position: fixed;
            top: 30%;
            left: 50%;
            transform: translate(-50%, -30%);
            box-shadow: 0 0 20px rgba(0, 255, 255, 0.3);
            z-index: 10;
        }

        button {
            padding: 0.5rem 1rem;
            border: none;
            border-radius: 5px;
            background-color: #50fa7b;
            color: #282a36;
            cursor: pointer;
            margin-top: 1rem;
        }
    </style>
</head>
<body>
    <div id="display-text">{{ $text->content }}</div>
    <input id="input-box" type="text" autofocus>
    <div id="wpm">WPM: 0</div>
    <div id="accuracy">Accuracy: 100%</div>
    <div id="errors">Errors: 0</div>
    <div id="timer">Time: 00:00:000</div>

    <audio id="sound-correct" src="/sounds/correct.mp3" preload="auto"></audio>
    <audio id="sound-error" src="/sounds/error.mp3" preload="auto"></audio>

    <form method="POST" action="{{ route('submit') }}">
        @csrf
        <input type="hidden" name="nickname" value="Player1">
        <input type="hidden" name="difficulty" value="{{ $difficulty }}">
        <input type="hidden" id="completion_time" name="completion_time">
        <button type="submit" id="submit-btn" style="display: none;">Submit</button>
    </form>

    <div id="result-modal">
        <h2>Finished!</h2>
        <p id="final-wpm"></p>
        <p id="final-accuracy"></p>
        <p id="final-time"></p>
        <p id="final-badge" style="margin-top: 1rem; font-weight: bold; font-size: 1.2rem;"></p>
        <button onclick="location.reload()">Play Again</button>
    </div>

    <script>
        let startTime, timerInterval, started = false;
        let errorCount = 0;
        let errorTracker = {}; // Track index-based errors only once

        const inputBox = document.getElementById('input-box');
        const originalText = document.getElementById('display-text').innerText;
        const correctSound = document.getElementById('sound-correct');
        const errorSound = document.getElementById('sound-error');

        window.onload = () => inputBox.focus();
        document.body.addEventListener('click', () => inputBox.focus());

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
                    resultHTML += `<span style="color: green;">${origChar}</span>`;
                } else {
                    resultHTML += `<span style="color: red;">${origChar}</span>`;

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
            document.getElementById('submit-btn').style.display = 'block';

            const wpmText = document.getElementById('wpm').textContent;
            const accuracyText = document.getElementById('accuracy').textContent;

            document.getElementById('final-wpm').textContent = wpmText;
            document.getElementById('final-accuracy').textContent = accuracyText;
            document.getElementById('final-time').textContent = 'Time: ' + formatTime(timeTaken);

            const wpmValue = parseInt(wpmText.replace(/\D/g, ''));
            let badgeText = '';
            if (wpmValue < 30) badgeText = '🐢 Beginner';
            else if (wpmValue < 60) badgeText = '🐇 Intermediate';
            else if (wpmValue < 100) badgeText = '🦅 Advanced';
            else badgeText = '🔥 Typing God';

            document.getElementById('final-badge').textContent = badgeText;
            document.getElementById('result-modal').style.display = 'block';
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
