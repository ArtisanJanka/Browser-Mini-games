<x-app-layout>
    <header class="flex justify-center mb-6 text-lg font-semibold">
        <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
            <h1 class="text-2xl font-bold text-cyan-300">Flappy Bird</h1>
        </div>
    </header>

    <!-- Preload Assets -->
    @foreach (['bird1', 'bird2', 'bird3', 'bg', 'fg', 'pipeNorth', 'pipeSouth'] as $img)
        <link rel="preload" as="image" href="{{ asset("images/$img.png") }}" />
    @endforeach
    <audio id="sound-fly" src="/sounds/fly.mp3" preload="auto"></audio>
    <audio id="sound-score" src="/sounds/score.mp3" preload="auto"></audio>
    <audio id="sound-game-over" src="https://assets.mixkit.co/sfx/download/mixkit-wrong-answer-buzz-950.mp3" preload="auto"></audio>

    <div class="max-w-3xl mx-auto text-center">
        <!-- Start Screen -->
        <div id="start-screen" class="bg-[#2d2d44] p-6 rounded-xl shadow-md">
            <h2 class="text-xl font-bold mb-4 text-yellow-300">Choose Your Bird</h2>
            <div class="mb-6 text-left bg-[#1e1e2f] p-4 rounded-lg">
                <h3 class="text-lg font-semibold mb-2 text-green-300">How to Play:</h3>
                <ul class="space-y-1 text-sm text-white">
                    <li><span class="text-yellow-300">• Press Space or Tap</span> to fly</li>
                    <li><span class="text-yellow-300">• Avoid pipes</span> to stay alive</li>
                    <li><span class="text-yellow-300">• Score points</span> by passing through pipes</li>
                </ul>
            </div>
            <div id="bird-select" class="flex justify-center gap-4 mb-6">
                @foreach ([1 => 'Classic', 2 => 'Golden', 3 => 'Blue'] as $num => $label)
                    <div class="text-white">
                        <img src="{{ asset("images/bird$num.png") }}" 
                             class="bird-option {{ $num === 1 ? 'selected' : '' }} w-16 h-auto cursor-pointer border-2 border-transparent hover:scale-110 transition duration-200 rounded-lg" 
                             data-src="{{ asset("images/bird$num.png") }}">
                        <p class="mt-2 text-xs">{{ $label }}</p>
                    </div>
                @endforeach
            </div>
            <button id="play-btn" class="bg-yellow-400 text-black px-6 py-2 rounded-lg hover:bg-yellow-300 transition font-bold">Play Game</button>
        </div>

        <!-- Game Screen -->
        <div id="game-screen" class="hidden mt-6">
            <div class="game-stats flex justify-between mb-4 bg-[#2d2d44] p-3 rounded-lg text-white">
                <p id="score-display" class="font-bold text-yellow-300">Score: 0</p>
                <p id="high-score-display" class="font-bold text-cyan-300">High Score: 0</p>
            </div>
            <canvas id="canvas" width="288" height="512" class="rounded-lg mx-auto border-2 border-[#2d2d44]"></canvas>
        </div>

        <!-- Game Over Modal -->
        <div id="game-over-modal" class="hidden fixed top-[30%] left-1/2 transform -translate-x-1/2 bg-[#282846] text-white border-2 border-cyan-300 p-8 rounded-xl shadow-2xl z-50 w-full max-w-md text-center">
            <h2 class="text-xl font-bold text-red-400">Game Over!</h2>
            <p id="final-score" class="mt-4 text-lg font-bold text-yellow-300">Score: 0</p>
            <p id="final-high-score" class="text-cyan-300">High Score: 0</p>
            <form id="score-form" method="POST" action="/submit-score">
                @csrf
                <input type="hidden" name="score" id="score-input">
                <button type="submit" id="save-score-btn"
                    class="mt-4 bg-yellow-400 text-black px-4 py-2 rounded-lg hover:bg-yellow-300 font-bold">
                    Save Score
                </button>
            </form>
            <div class="mt-6 flex justify-center gap-4">
                <button id="restart-btn" class="bg-green-400 text-black px-4 py-2 rounded-lg hover:bg-green-300 font-bold">Play Again</button>
                <button id="back-btn" class="bg-red-400 text-black px-4 py-2 rounded-lg hover:bg-red-300 font-bold">Back to Menu</button>
            </div>
        </div>
    </div>

    <!-- Game Logic -->
    <script>
        let selectedBird = "{{ asset('images/bird1.png') }}";
        let cvs = document.getElementById("canvas");
        let ctx = cvs.getContext("2d");

        let bg = new Image(), fg = new Image(), pipeNorth = new Image(), pipeSouth = new Image(), bird = new Image();
        let fly = document.getElementById("sound-fly");
        let scoreSound = document.getElementById("sound-score");
        let gameOverSound = document.getElementById("sound-game-over");

        bg.src = "{{ asset('images/bg.png') }}";
        fg.src = "{{ asset('images/fg.png') }}";
        pipeNorth.src = "{{ asset('images/pipeNorth.png') }}";
        pipeSouth.src = "{{ asset('images/pipeSouth.png') }}";

        let gap = 110, gravity = 1.8, score = 0, highScore = localStorage.getItem("flappyHighScore") || 0;
        let bX = 10, bY = 150, pipes = [];

        document.querySelectorAll('.bird-option').forEach(option => {
            option.addEventListener('click', () => {
                document.querySelectorAll('.bird-option').forEach(o => o.classList.remove('selected', 'border-yellow-300'));
                option.classList.add('selected', 'border-yellow-300');
                selectedBird = option.dataset.src;
            });
        });

        document.getElementById('play-btn').addEventListener('click', () => {
            document.getElementById('start-screen').classList.add('hidden');
            document.getElementById('game-screen').classList.remove('hidden');
            startGame();
        });

        document.getElementById('restart-btn').addEventListener('click', () => {
            document.getElementById('game-over-modal').classList.add('hidden');
            startGame();
        });

        document.getElementById('back-btn').addEventListener('click', () => {
            document.getElementById('game-over-modal').classList.add('hidden');
            document.getElementById('game-screen').classList.add('hidden');
            document.getElementById('start-screen').classList.remove('hidden');
        });

        function startGame() {
            bird.src = selectedBird;
            bX = 10; bY = 150; score = 0; pipes = [{ x: cvs.width, y: 0 }];
            document.getElementById('score-display').textContent = `Score: 0`;
            document.getElementById('high-score-display').textContent = `High Score: ${highScore}`;
            draw();
        }

        document.addEventListener("keydown", () => { bY -= 45; fly.play(); });
        cvs.addEventListener("click", () => { bY -= 45; fly.play(); });

        function draw() {
            ctx.drawImage(bg, 0, 0);
            for (let i = 0; i < pipes.length; i++) {
                let constant = pipeNorth.height + gap;
                ctx.drawImage(pipeNorth, pipes[i].x, pipes[i].y);
                ctx.drawImage(pipeSouth, pipes[i].x, pipes[i].y + constant);
                pipes[i].x--;

                if (pipes[i].x === 125) {
                    pipes.push({ x: cvs.width, y: Math.floor(Math.random() * pipeNorth.height) - pipeNorth.height });
                }

                // Collision detection
                if (
                    bX + bird.width >= pipes[i].x && bX <= pipes[i].x + pipeNorth.width &&
                    (bY <= pipes[i].y + pipeNorth.height || bY + bird.height >= pipes[i].y + constant) ||
                    bY + bird.height >= cvs.height - fg.height
                ) {
                    endGame();
                    return;
                }

                if (pipes[i].x === 5) {
                    score++;
                    scoreSound.play();
                }
            }

            ctx.drawImage(fg, 0, cvs.height - fg.height);
            ctx.drawImage(bird, bX, bY);
            bY += gravity;

            ctx.fillStyle = "#fff";
            ctx.font = "20px Verdana";
            ctx.fillText("Score : " + score, 10, cvs.height - 20);

            requestAnimationFrame(draw);
        }

        function endGame() {
            gameOverSound.play();
            document.getElementById("final-score").textContent = `Score: ${score}`;
            if (score > highScore) {
                highScore = score;
                localStorage.setItem("flappyHighScore", highScore);
            }
            document.getElementById("final-high-score").textContent = `High Score: ${highScore}`;
            document.getElementById("game-over-modal").classList.remove("hidden");
            document.getElementById("score-input").value = score;
        }
    </script>
</x-app-layout>
