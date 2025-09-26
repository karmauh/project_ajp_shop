// pobranie elementów HTML
const canvas = document.getElementById('gameCanvas');
const ctx = canvas.getContext('2d');
const scoreDisplay = document.getElementById('score');
const menu = document.getElementById('menu');

// piłka z bazowymi parametrami
let ball = {
    x: canvas.width / 2,
    y: canvas.height / 2,
    radius: 10,
    speedX: 0,
    speedY: 0
};

// gracz z bazowymi parametrami
let player = {
    x: 10,
    y: canvas.height / 2 - 50,
    width: 10,
    height: 100,
    speed: 8,
    score: 0
};

// komputer z bazowym parametrami
let computer = {
    x: canvas.width - 20,
    y: canvas.height / 2 - 50,
    width: 10,
    height: 100,
    speed: 6,
    score: 0
};

// wciskanie klawiszy (strzałek)
let keys = {
    ArrowUp: false,
    ArrowDown: false
};

let gameRunning = false;
let animationFrameId;

// ustawienie poziomu trudności
function setDifficulty(level) {
    if (level === 'easy') {
        ball.speedX = 5;
        ball.speedY = 3;
        player.height = 120;
        computer.height = 120;
        computer.speed = 4;
    } else if (level === 'medium') {
        ball.speedX = 7;
        ball.speedY = 5;
        player.height = 100;
        computer.height = 100;
        computer.speed = 6;
    } else if (level === 'hard') {
        ball.speedX = 10;
        ball.speedY = 7;
        player.height = 80;
        computer.height = 80;
        computer.speed = 8;
    }
}

// rozpoczęcie gry
function startGame(level) {
    menu.style.display = 'none'; // ukrycie menu
    resetGame();
    setDifficulty(level);
    gameRunning = true;
    animate(); // uruchomienie gry (petla)
}

// reset pozycji piłki i paletek
function resetGame() {
    ball.x = canvas.width / 2;
    ball.y = canvas.height / 2;
    
    // Losowy kierunek startowy piłki
    // upewniamy się że prędkość jest dodatnia
    // mnożymy przez losową liczbę z zakresu (0, 1)
    // jeżeli wylosowana liczba jest większa niż 0.5 zwraca 1 => kierunek prawo
    // w przeciwnym wypadku -1 => lewo
    ball.speedX = Math.abs(ball.speedX) * (Math.random() > 0.5 ? 1 : -1);
    ball.speedY = Math.abs(ball.speedY) * (Math.random() > 0.5 ? 1 : -1);
    player.y = canvas.height / 2 - player.height / 2;
    computer.y = canvas.height / 2 - computer.height / 2;
}

// rysowanie elementów gry
function draw() {
    ctx.clearRect(0, 0, canvas.width, canvas.height);

    // piłka
    ctx.beginPath();
    ctx.arc(ball.x, ball.y, ball.radius, 0, Math.PI * 2);
    ctx.fillStyle = '#fff';
    ctx.fill();
    ctx.closePath();

    // paletki
    ctx.fillStyle = '#fff';
    ctx.fillRect(player.x, player.y, player.width, player.height);
    ctx.fillRect(computer.x, computer.y, computer.width, computer.height);

    // linia środkowa przerywana
    ctx.setLineDash([5, 15]);
    ctx.beginPath();
    ctx.moveTo(canvas.width / 2, 0);
    ctx.lineTo(canvas.width / 2, canvas.height);
    ctx.strokeStyle = '#fff';
    ctx.stroke();
    ctx.setLineDash([]);
}

// aktualizacja stanu gry
function update() {
    // ruch piłki
    ball.x += ball.speedX;
    ball.y += ball.speedY;

    // odbicia od krawędzi
    if (ball.y + ball.radius > canvas.height || ball.y - ball.radius < 0) {
        ball.speedY = -ball.speedY;
    }

    // sprawdzenie punktów
    if (ball.x + ball.radius > canvas.width) {
        player.score++;
        updateScore();
        resetGame();
    } else if (ball.x - ball.radius < 0) {
        computer.score++;
        updateScore();
        resetGame();
    }

    // odbicie od paletki gracza
    if (
        ball.x - ball.radius < player.x + player.width &&
        ball.y > player.y &&
        ball.y < player.y + player.height
    ) {
        ball.speedX = -ball.speedX;
    }

    // odbicie od paletki komputera
    if (
        ball.x + ball.radius > computer.x &&
        ball.y > computer.y &&
        ball.y < computer.y + computer.height
    ) {
        ball.speedX = -ball.speedX;
    }

    // ruch gracza
    if (keys.ArrowUp && player.y > 0) {
        player.y -= player.speed;
    }
    if (keys.ArrowDown && player.y + player.height < canvas.height) {
        player.y += player.speed;
    }

    // prosty algorytm ruchu komputera (podąża za piłką - nie da się wygrać...)
    if (computer.y + computer.height / 2 < ball.y) {
        computer.y += computer.speed;
    } else if (computer.y + computer.height / 2 > ball.y) {
        computer.y -= computer.speed;
    }

    // ograniczenie ruchu w pionie
    player.y = Math.max(0, Math.min(canvas.height - player.height, player.y));
    computer.y = Math.max(0, Math.min(canvas.height - computer.height, computer.y));
}

// aktualizacja wyniku na ekranie
function updateScore() {
    scoreDisplay.textContent = `Gracz: ${player.score} Komputer: ${computer.score}`;
}

// pętla gry
function animate() {
    if (gameRunning) {
        update();
        draw();
        animationFrameId = requestAnimationFrame(animate);
    }
}

// obsługa wciśnięcia klawiszy
document.addEventListener('keydown', (e) => {
    if (e.key === 'ArrowUp' || e.key === 'ArrowDown') {
        keys[e.key] = true;
    }
});

// obsługa puszczenia klawiszy
document.addEventListener('keyup', (e) => {
    if (e.key === 'ArrowUp' || e.key === 'ArrowDown') {
        keys[e.key] = false;
    }
});