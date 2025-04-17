<!DOCTYPE html>
<html>
<head>
    <title>Flappy Bird | Choose Your Bird</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            text-align: center;
            background: #70c5ce;
            margin: 0;
            padding: 0;
        }
        #game-screen {
            display: none;
        }
        .bird-option {
            width: 60px;
            height: auto;
            margin: 10px;
            cursor: pointer;
            border: 3px solid transparent;
            border-radius: 8px;
            transition: 0.2s;
        }
        .bird-option:hover {
            transform: scale(1.1);
        }
        .bird-option.selected {
            border-color: #fcd34d;
        }
        #play-btn {
            margin-top: 20px;
            padding: 10px 20px;
            font-size: 18px;
            background: #facc15;
            border: none;
            border-radius: 8px;
            cursor: pointer;
        }
        canvas {
            margin-top: 20px;
            border-radius: 8px;
        }
    </style>
</head>
<body>
    <div id="start-screen">
        <h2>Choose Your Bird</h2>
        <div id="bird-select">
            <img src="{{ asset('images/bird1.png') }}" class="bird-option selected" data-src="{{ asset('images/bird1.png') }}">
            <img src="{{ asset('images/bird2.png') }}" class="bird-option" data-src="{{ asset('images/bird2.png') }}">
            <img src="{{ asset('images/bird3.png') }}" class="bird-option" data-src="{{ asset('images/bird3.png') }}">
        </div>
        <button id="play-btn">Play</button>
    </div>

    <div id="game-screen">
        <canvas id="canvas" width="288" height="512"></canvas>
    </div>

    <script>
        let selectedBirdSrc = document.querySelector('.bird-option.selected').dataset.src;
        const birdOptions = document.querySelectorAll('.bird-option');

        birdOptions.forEach(option => {
            option.addEventListener('click', function () {
                birdOptions.forEach(o => o.classList.remove('selected'));
                this.classList.add('selected');
                selectedBirdSrc = this.dataset.src;
            });
        });

        document.getElementById('play-btn').addEventListener('click', function () {
            document.getElementById('start-screen').style.display = 'none';
            document.getElementById('game-screen').style.display = 'block';

            // Set global variable for birdSkin before loading the game
            window.birdSkin = selectedBirdSrc;

            // Dynamically load the game script
            const script = document.createElement('script');
            script.src = "/js/flappyBird.js";
            document.body.appendChild(script);
        });
    </script>
</body>
</html>
