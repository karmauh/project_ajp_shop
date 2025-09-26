// 5 sekund przed rozpoczęciem gry
setTimeout(startGame, 5000);

function startGame() {
  // ukrywam ekran ładowania (element z id="loading")
  document.getElementById("loading").style.display = "none";
  
  // pokazuje element canvas z grą (element z id="game")
  document.getElementById("game").style.display = "block";

  // funkcja rozpoczynajaca gre
  initGame();
}

// główna funkcja
function initGame() {
  const canvas = document.getElementById("game");
  const ctx = canvas.getContext("2d");  // pobieram kontekst rysowania 2D

  const scale = 20;  // Rozmiar jednej komórki w pikselach
  const rows = canvas.height / scale;  // liczba wierszy na planszy
  const columns = canvas.width / scale;  // liczba kolumn na planszy

  let snake;
  let fruit;

  // Klasa reprezentująca węża
  class Snake {
    constructor() {
      this.body = [{ x: 10, y: 10 }];  // Pozycja początkowa węża (jeden segment)
      this.xSpeed = 1;  // początkowy kierunek prawo
      this.ySpeed = 0;
    }

    // Rysowanie węża na planszy
    draw() {
      ctx.fillStyle = "#0f0"; // kolor węża
      this.body.forEach(part => {
        // każdy segment węża
        ctx.fillRect(part.x * scale, part.y * scale, scale, scale);
      });
    }

    // Aktualizacja pozycji węża
    update() {
      const head = {
        x: this.body[0].x + this.xSpeed,
        y: this.body[0].y + this.ySpeed
      };
      this.body.unshift(head);  // nowa głowa do początku ciała

      // jeśli wąż zjadł owocek
      if (head.x === fruit.x && head.y === fruit.y) {
        placeFruit();  // nowy owoc
      } else {
        this.body.pop();  // usuwam ostatni segment
      }
    }

    // kierowanie ruchu węża
    changeDirection(dir) {
      switch (dir) {
        case "ArrowUp":
          if (this.ySpeed === 0) {  // nie można skręcić do tyłu (ale sie da)
            this.xSpeed = 0;
            this.ySpeed = -1;
          }
          break;
        case "ArrowDown":
          if (this.ySpeed === 0) {
            this.xSpeed = 0;
            this.ySpeed = 1;
          }
          break;
        case "ArrowLeft":
          if (this.xSpeed === 0) {
            this.xSpeed = -1;
            this.ySpeed = 0;
          }
          break;
        case "ArrowRight":
          if (this.xSpeed === 0) {
            this.xSpeed = 1;
            this.ySpeed = 0;
          }
          break;
      }
    }

    // spr. czy wąż uderzył w ścianę lub siebie
    checkCollision() {
      const [head, ...body] = this.body;  // rozbijam ciało na głowę i resztę
      for (const part of body) {
        // spr. kolizję z własnym ciałem
        if (head.x === part.x && head.y === part.y) return true;
      }
      // sp. kolizję ze ścianami
      return (
        head.x < 0 || head.y < 0 ||
        head.x >= columns || head.y >= rows
      );
    }
  }

  // losowe umieszczanie owocu na planszy
  function placeFruit() {
    fruit = {
      x: Math.floor(Math.random() * columns),
      y: Math.floor(Math.random() * rows)
    };
  }

  // rysowanie owocu na planszy
  function drawFruit() {
    ctx.fillStyle = "red";  // Czerwony kolor owocu
    ctx.fillRect(fruit.x * scale, fruit.y * scale, scale, scale);
  }

  // główna pętla gry
  function gameLoop() {
    ctx.clearRect(0, 0, canvas.width, canvas.height);  // czyszczenie planszy
    snake.update();  // aktualizacja pozycji węża
    if (snake.checkCollision()) {
      alert("Game Over!"); 
      snake = new Snake();  // nowa grę
      placeFruit();  // nowy owoc
    }

    snake.draw();
    drawFruit();
  }

  // zdarzenie naciśnięcia klawisza
  window.addEventListener("keydown", e => {
    snake.changeDirection(e.key);  // zmiana kierunku ruchu węża
  });

  snake = new Snake();  // utworznei nowego węża
  placeFruit();  // umieszczenie pierwszego owocu
  setInterval(gameLoop, 150);  // Uruchamiaj gameLoop co 150 ms
}
