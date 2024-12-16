// Переменная для отслеживания состояния игры
let gameOver = false;

// Функция для броска кубиков
// Функция для броска кубиков
function rollDice() {
    if (gameOver) return;

    var dice1 = Math.floor(Math.random() * 6) + 1;
    var dice2 = Math.floor(Math.random() * 6) + 1;
    var diceResult = dice1 + dice2;

    // Обновляем изображения кубиков
    document.getElementById('dice1').style.backgroundImage = 'url("public/images/dice' + dice1 + '.png")';
    document.getElementById('dice2').style.backgroundImage = 'url("public/images/dice' + dice2 + '.png")';

    // Скрываем кнопку броска
    document.querySelector('button').style.display = 'none';

    // Отправляем запрос на сервер
    fetch('/update-player-position', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ diceResult: diceResult })
    })
        .then(response => response.json())
        .then(data => {
            if (data.error) {
                console.error(data.error);
                return;
            }

            // Начальная позиция
            const playerChip = document.querySelector('.chip_player');
            let currentX = parseInt(playerChip.style.left);
            let currentY = parseInt(playerChip.style.top);

            // Список шагов
            const steps = data.steps;

            let currentStep = 0;
            const interval = setInterval(() => {
                if (currentStep >= steps.length) {
                    clearInterval(interval);

                    // Проверка на победу
                    if (data.winMessage) {
                        alert(data.winMessage);
                        gameOver = true;
                        showRestartButton();
                    } else {
                        setTimeout(() => moveBot(), 1000);  // Переход к ходу бота
                    }
                } else {
                    const { x, y } = steps[currentStep];
                    currentX = x + 30;  // Корректируем сдвиг
                    currentY = y + 30;  // Корректируем сдвиг

                    // Плавное движение
                    playerChip.style.transition = 'top 0.3s, left 0.3s';  // Плавное движение
                    playerChip.style.left = `${currentX}px`;
                    playerChip.style.top = `${currentY}px`;

                    currentStep++;
                }
            }, 300);  // Интервал для каждого шага (300ms)
        })
        .catch(error => console.error('Ошибка:', error));
}


// Функция для отображения кнопки "Начать заново"
function showRestartButton() {
    // Создаем кнопку "Начать заново"
    const restartButton = document.createElement('button');
    restartButton.textContent = 'Начать заново';
    restartButton.onclick = function () {
        resetGame();  // Вызовем функцию для сброса игры
    };

    // Стилевое позиционирование кнопки
    restartButton.style.position = 'absolute';
    restartButton.style.top = '350px'; // Отступ от верхней границы, под картинкой
    restartButton.style.left = '30px'; // Отступ слева
    restartButton.style.padding = '10px 20px';
    restartButton.style.backgroundColor = '#4CAF50'; // Зеленый цвет
    restartButton.style.color = 'white';
    restartButton.style.border = 'none';
    restartButton.style.borderRadius = '5px';
    restartButton.style.fontSize = '16px';

    // Добавляем кнопку на страницу
    document.body.appendChild(restartButton);
}

// Функция для сброса игры
function resetGame() {
    fetch('/reset-game', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
        },
    })
        .then(response => response.json())
        .then(data => {
            alert(data.message);  // Показать сообщение о сбросе
            location.reload();  // Перезагружаем страницу для перезапуска игры
        })
        .catch(error => console.error('Ошибка:', error));
}

function moveBot() {
    // Проверяем, завершена ли игра
    if (gameOver) return;

    fetch('/move-bot', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
        },
    })
        .then(response => response.json())
        .then(data => {
            if (data.error) {
                console.error(data.error);
                return;
            }

            // Получаем количество клеток для движения
            const steps = data.steps;
            const botChip = document.querySelector('.chip_bot');

            // Обновляем фишку через каждый шаг
            let currentStep = 0;

            function moveToNextStep() {
                if (currentStep < steps.length) {
                    const { x, y } = steps[currentStep];
                    botChip.style.transition = 'top 0.3s, left 0.3s';  // Плавное движение
                    botChip.style.top = (y + 70) + 'px';
                    botChip.style.left = (x + 30) + 'px';
                    currentStep++;
                    setTimeout(moveToNextStep, 300); // Переход к следующему шагу через 0.3 секунды
                } else {
                    // Когда шаги завершены, обновляем местоположение фишки
                    const { x, y } = data.botPosition;
                    botChip.style.top = (y + 70) + 'px';
                    botChip.style.left = (x + 30) + 'px';

                    // Если бот победил, показать сообщение
                    if (data.winMessage) {
                        alert(data.winMessage);
                        gameOver = true;
                        showRestartButton();
                    } else {
                        // Показываем кнопку для следующего хода игрока
                        document.querySelector('button').style.display = 'inline-block';  // Показываем кнопку
                    }
                }
            }

            moveToNextStep();  // Запускаем движение
        })
        .catch(error => console.error('Ошибка:', error));
}

