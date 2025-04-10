<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>
<body>
    <div id="display-text">{{ $text->content }}</div>
    <input id="input-box" type="text">
    <div id="wpm">WPM: 0</div>
    <div id="timer">Time: 00:00:000</div>

    <form method="POST" action="{{ route('submit') }}">
        @csrf
        <input type="hidden" name="nickname" value="Player1">
        <input type="hidden" name="difficulty" value="{{ $difficulty }}">
        <input type="hidden" id="completion_time" name="completion_time">
        <button type="submit" id="submit-btn" style="display: none;">Submit</button>
    </form>

    <script>
    let startTime, timerInterval;
    let started = false;
    const inputBox = document.getElementById('input-box');
    const displayText = document.getElementById('display-text').innerText;
    const displayWords = displayText.split(' ');

    inputBox.addEventListener('input', () => {
        if (!started) {
            started = true;
            startTime = Date.now();
            timerInterval = setInterval(() => {
                const elapsed = Date.now() - startTime;
                document.getElementById('timer').textContent = 'Time: ' + formatTime(elapsed);
            }, 100);
        }

        const typedWords = inputBox.value.split(' ');
        const wordSpans = displayWords.map((word, index) => {
            if (typedWords[index] === undefined) return `<span>${word}</span>`;
            if (typedWords[index] === word) return `<span style='color: green;'>${word}</span>`;
            return `<span style='color: red;'>${word}</span>`;
        });

        document.getElementById('display-text').innerHTML = wordSpans.join(' ');

        if (typedWords.length >= displayWords.length) {
            clearInterval(timerInterval);
            const timeTaken = Date.now() - startTime;
            document.getElementById('completion_time').value = timeTaken;
            document.getElementById('submit-btn').style.display = 'block';
        }
    });

    function formatTime(ms) {
        const minutes = String(Math.floor(ms / 60000)).padStart(2, '0');
        const seconds = String(Math.floor((ms % 60000) / 1000)).padStart(2, '0');
        const milliseconds = String(ms % 1000).padStart(3, '0');
        return `${minutes}:${seconds}:${milliseconds}`;
    }
</script>
</body>
</html>