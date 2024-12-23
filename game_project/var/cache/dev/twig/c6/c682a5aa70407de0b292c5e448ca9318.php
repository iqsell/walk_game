<?php

use Twig\Environment;
use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Extension\CoreExtension;
use Twig\Extension\SandboxExtension;
use Twig\Markup;
use Twig\Sandbox\SecurityError;
use Twig\Sandbox\SecurityNotAllowedTagError;
use Twig\Sandbox\SecurityNotAllowedFilterError;
use Twig\Sandbox\SecurityNotAllowedFunctionError;
use Twig\Source;
use Twig\Template;
use Twig\TemplateWrapper;

/* field/index.html.twig */
class __TwigTemplate_ac05a1fb272991842b2de81f437f9dc1 extends Template
{
    private Source $source;
    /**
     * @var array<string, Template>
     */
    private array $macros = [];

    public function __construct(Environment $env)
    {
        parent::__construct($env);

        $this->source = $this->getSourceContext();

        $this->parent = false;

        $this->blocks = [
        ];
    }

    protected function doDisplay(array $context, array $blocks = []): iterable
    {
        $macros = $this->macros;
        $__internal_6f47bbe9983af81f1e7450e9a3e3768f = $this->extensions["Symfony\\Bridge\\Twig\\Extension\\ProfilerExtension"];
        $__internal_6f47bbe9983af81f1e7450e9a3e3768f->enter($__internal_6f47bbe9983af81f1e7450e9a3e3768f_prof = new \Twig\Profiler\Profile($this->getTemplateName(), "template", "field/index.html.twig"));

        // line 1
        yield "<!DOCTYPE html>
<html lang=\"en\">
<head>
    <meta charset=\"UTF-8\">
    <meta name=\"viewport\" content=\"width=device-width, initial-scale=1.0\">
    <title>Бродилка</title>
    <style>
        /* Общие стили */
        .center-text {
            text-align: center;
            margin-top: 20px;
        }

        .game-field {
            width: 100%;
            max-width: 1400px;
            height: auto;
            display: block;
            margin: 0 auto;
        }

        /* Стиль фишек */
        .chip {
            position: absolute;
            width: 50px; /* Размер фишки */
            height: 50px; /* Размер фишки */
        }

        .chip_bot {
            background-image: url('public/images/chip_bot.png');
            background-size: cover;
        }

        .chip_player {
            background-image: url('public/images/chip_player.png');
            background-size: cover;
        }

        /* Стиль кубиков */
        .dice {
            width: 50px;
            height: 50px;
            display: inline-block;
            background-size: cover;
            margin: 0 10px;
        }

        /* Стиль помощи */
        .help-img {
            width: 100%;
            max-width: 400px;
            height: auto;
            display: block;
            margin: 0 auto;
        }
    </style>
</head>
<body>
<h1>Бродилка</h1>
<!-- Основная картинка -->
<div id=\"game-field\">
    <img src=\"public/images/field.jpg\" alt=\"Game Field\" class=\"game-field\">

    <!-- Фишки, -->
    ";
        // line 65
        $context['_parent'] = $context;
        $context['_seq'] = CoreExtension::ensureTraversable((isset($context["coordinates"]) || array_key_exists("coordinates", $context) ? $context["coordinates"] : (function () { throw new RuntimeError('Variable "coordinates" does not exist.', 65, $this->source); })()));
        foreach ($context['_seq'] as $context["_key"] => $context["field"]) {
            // line 66
            yield "        ";
            if (CoreExtension::getAttribute($this->env, $this->source, $context["field"], "isBot", [], "any", false, false, false, 66)) {
                // line 67
                yield "            <div class=\"chip chip_bot\" style=\"top: ";
                yield $this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape((CoreExtension::getAttribute($this->env, $this->source, $context["field"], "y", [], "any", false, false, false, 67) + 70), "html", null, true);
                yield "px; left: ";
                yield $this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape((CoreExtension::getAttribute($this->env, $this->source, $context["field"], "x", [], "any", false, false, false, 67) + 30), "html", null, true);
                yield "px;\"></div>
        ";
            } else {
                // line 69
                yield "            <div class=\"chip chip_player\" style=\"top: ";
                yield $this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape((CoreExtension::getAttribute($this->env, $this->source, $context["field"], "y", [], "any", false, false, false, 69) + 30), "html", null, true);
                yield "px; left: ";
                yield $this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape((CoreExtension::getAttribute($this->env, $this->source, $context["field"], "x", [], "any", false, false, false, 69) + 30), "html", null, true);
                yield "px;\"></div>
        ";
            }
            // line 71
            yield "    ";
        }
        $_parent = $context['_parent'];
        unset($context['_seq'], $context['_key'], $context['field'], $context['_parent']);
        $context = array_intersect_key($context, $_parent) + $_parent;
        // line 72
        yield "
</div>

<!-- Кубики -->
<h2 class=\"center-text\">Бросок кубиков</h2>
<div class=\"center-text\">
    <button onclick=\"rollDice()\">Бросить кубики</button>
</div>
<div id=\"dice-container\" class=\"center-text\">
    <div id=\"dice1\" class=\"dice\"></div>
    <div id=\"dice2\" class=\"dice\"></div>
</div>

<!-- Картинка с помощью -->
<h2 class=\"center-text\">Помощь</h2>
<img src=\"public/images/help.jpg\" alt=\"Help\" class=\"help-img\">

<script>
    let gameOver = false;

    // Функция для броска кубиков
    function rollDice() {
        if (gameOver) return;

        var dice1 = Math.floor(Math.random() * 6) + 1;
        var dice2 = Math.floor(Math.random() * 6) + 1;
        var diceResult = dice1 + dice2;

        // Обновляем изображения кубиков
        document.getElementById('dice1').style.backgroundImage = 'url(\"public/images/dice' + dice1 + '.png\")';
        document.getElementById('dice2').style.backgroundImage = 'url(\"public/images/dice' + dice2 + '.png\")';

        // Скрываем кнопку броска
        document.querySelector('button').style.display = 'none';

        // запрос на сервер
        fetch('/update-player-position', {
            method: 'POST',
            headers: {'Content-Type': 'application/json'},
            body: JSON.stringify({diceResult: diceResult})
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
                        const {x, y} = steps[currentStep];
                        currentX = x + 30;  // сдвиг
                        currentY = y + 30;  // сдвиг

                        // Плавное движение
                        playerChip.style.transition = 'top 0.3s, left 0.3s';  // Плавное движение
                        playerChip.style.left = `\${currentX}px`;
                        playerChip.style.top = `\${currentY}px`;

                        currentStep++;
                    }
                }, 300);
            })
            .catch(error => console.error('Ошибка:', error));
    }


    // Начать заново
    function showRestartButton() {
        // Начать заново
        const restartButton = document.createElement('button');
        restartButton.textContent = 'Начать заново';
        restartButton.onclick = function () {
            resetGame();  // для сброса игры
        };

        // Стили кнопки
        restartButton.style.position = 'absolute';
        restartButton.style.top = '350px';
        restartButton.style.left = '30px';
        restartButton.style.padding = '10px 20px';
        restartButton.style.backgroundColor = '#4CAF50';
        restartButton.style.color = 'white';
        restartButton.style.border = 'none';
        restartButton.style.borderRadius = '5px';
        restartButton.style.fontSize = '16px';

        document.body.appendChild(restartButton);
    }

    //сброс игры
    function resetGame() {
        fetch('/reset-game', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
        })
            .then(response => response.json())
            .then(data => {
                alert(data.message);  // сброс
                location.reload();
            })
            .catch(error => console.error('Ошибка:', error));
    }

    function moveBot() {
        // завершена ли игра
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

                // Получаем количество клеток
                const steps = data.steps;
                const botChip = document.querySelector('.chip_bot');

                let currentStep = 0;

                function moveToNextStep() {
                    if (currentStep < steps.length) {
                        const {x, y} = steps[currentStep];
                        botChip.style.transition = 'top 0.3s, left 0.3s';  // Плавное движение
                        botChip.style.top = (y + 70) + 'px';
                        botChip.style.left = (x + 30) + 'px';
                        currentStep++;
                        setTimeout(moveToNextStep, 300);
                    } else {
                        // Когда шаги завершены, обновляем фишки
                        const {x, y} = data.botPosition;
                        botChip.style.top = (y + 70) + 'px';
                        botChip.style.left = (x + 30) + 'px';

                        // Если бот победил
                        if (data.winMessage) {
                            alert(data.winMessage);
                            gameOver = true;
                            showRestartButton();
                        } else {
                            // кнопка для следующего хода игрока
                            document.querySelector('button').style.display = 'inline-block';
                        }
                    }
                }

                moveToNextStep();
            })
            .catch(error => console.error('Ошибка:', error));
    }


</script>

</body>
</html>
";
        
        $__internal_6f47bbe9983af81f1e7450e9a3e3768f->leave($__internal_6f47bbe9983af81f1e7450e9a3e3768f_prof);

        yield from [];
    }

    /**
     * @codeCoverageIgnore
     */
    public function getTemplateName(): string
    {
        return "field/index.html.twig";
    }

    /**
     * @codeCoverageIgnore
     */
    public function isTraitable(): bool
    {
        return false;
    }

    /**
     * @codeCoverageIgnore
     */
    public function getDebugInfo(): array
    {
        return array (  140 => 72,  134 => 71,  126 => 69,  118 => 67,  115 => 66,  111 => 65,  45 => 1,);
    }

    public function getSourceContext(): Source
    {
        return new Source("<!DOCTYPE html>
<html lang=\"en\">
<head>
    <meta charset=\"UTF-8\">
    <meta name=\"viewport\" content=\"width=device-width, initial-scale=1.0\">
    <title>Бродилка</title>
    <style>
        /* Общие стили */
        .center-text {
            text-align: center;
            margin-top: 20px;
        }

        .game-field {
            width: 100%;
            max-width: 1400px;
            height: auto;
            display: block;
            margin: 0 auto;
        }

        /* Стиль фишек */
        .chip {
            position: absolute;
            width: 50px; /* Размер фишки */
            height: 50px; /* Размер фишки */
        }

        .chip_bot {
            background-image: url('public/images/chip_bot.png');
            background-size: cover;
        }

        .chip_player {
            background-image: url('public/images/chip_player.png');
            background-size: cover;
        }

        /* Стиль кубиков */
        .dice {
            width: 50px;
            height: 50px;
            display: inline-block;
            background-size: cover;
            margin: 0 10px;
        }

        /* Стиль помощи */
        .help-img {
            width: 100%;
            max-width: 400px;
            height: auto;
            display: block;
            margin: 0 auto;
        }
    </style>
</head>
<body>
<h1>Бродилка</h1>
<!-- Основная картинка -->
<div id=\"game-field\">
    <img src=\"public/images/field.jpg\" alt=\"Game Field\" class=\"game-field\">

    <!-- Фишки, -->
    {% for field in coordinates %}
        {% if field.isBot %}
            <div class=\"chip chip_bot\" style=\"top: {{ field.y + 70 }}px; left: {{ field.x + 30 }}px;\"></div>
        {% else %}
            <div class=\"chip chip_player\" style=\"top: {{ field.y + 30 }}px; left: {{ field.x + 30 }}px;\"></div>
        {% endif %}
    {% endfor %}

</div>

<!-- Кубики -->
<h2 class=\"center-text\">Бросок кубиков</h2>
<div class=\"center-text\">
    <button onclick=\"rollDice()\">Бросить кубики</button>
</div>
<div id=\"dice-container\" class=\"center-text\">
    <div id=\"dice1\" class=\"dice\"></div>
    <div id=\"dice2\" class=\"dice\"></div>
</div>

<!-- Картинка с помощью -->
<h2 class=\"center-text\">Помощь</h2>
<img src=\"public/images/help.jpg\" alt=\"Help\" class=\"help-img\">

<script>
    let gameOver = false;

    // Функция для броска кубиков
    function rollDice() {
        if (gameOver) return;

        var dice1 = Math.floor(Math.random() * 6) + 1;
        var dice2 = Math.floor(Math.random() * 6) + 1;
        var diceResult = dice1 + dice2;

        // Обновляем изображения кубиков
        document.getElementById('dice1').style.backgroundImage = 'url(\"public/images/dice' + dice1 + '.png\")';
        document.getElementById('dice2').style.backgroundImage = 'url(\"public/images/dice' + dice2 + '.png\")';

        // Скрываем кнопку броска
        document.querySelector('button').style.display = 'none';

        // запрос на сервер
        fetch('/update-player-position', {
            method: 'POST',
            headers: {'Content-Type': 'application/json'},
            body: JSON.stringify({diceResult: diceResult})
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
                        const {x, y} = steps[currentStep];
                        currentX = x + 30;  // сдвиг
                        currentY = y + 30;  // сдвиг

                        // Плавное движение
                        playerChip.style.transition = 'top 0.3s, left 0.3s';  // Плавное движение
                        playerChip.style.left = `\${currentX}px`;
                        playerChip.style.top = `\${currentY}px`;

                        currentStep++;
                    }
                }, 300);
            })
            .catch(error => console.error('Ошибка:', error));
    }


    // Начать заново
    function showRestartButton() {
        // Начать заново
        const restartButton = document.createElement('button');
        restartButton.textContent = 'Начать заново';
        restartButton.onclick = function () {
            resetGame();  // для сброса игры
        };

        // Стили кнопки
        restartButton.style.position = 'absolute';
        restartButton.style.top = '350px';
        restartButton.style.left = '30px';
        restartButton.style.padding = '10px 20px';
        restartButton.style.backgroundColor = '#4CAF50';
        restartButton.style.color = 'white';
        restartButton.style.border = 'none';
        restartButton.style.borderRadius = '5px';
        restartButton.style.fontSize = '16px';

        document.body.appendChild(restartButton);
    }

    //сброс игры
    function resetGame() {
        fetch('/reset-game', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
        })
            .then(response => response.json())
            .then(data => {
                alert(data.message);  // сброс
                location.reload();
            })
            .catch(error => console.error('Ошибка:', error));
    }

    function moveBot() {
        // завершена ли игра
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

                // Получаем количество клеток
                const steps = data.steps;
                const botChip = document.querySelector('.chip_bot');

                let currentStep = 0;

                function moveToNextStep() {
                    if (currentStep < steps.length) {
                        const {x, y} = steps[currentStep];
                        botChip.style.transition = 'top 0.3s, left 0.3s';  // Плавное движение
                        botChip.style.top = (y + 70) + 'px';
                        botChip.style.left = (x + 30) + 'px';
                        currentStep++;
                        setTimeout(moveToNextStep, 300);
                    } else {
                        // Когда шаги завершены, обновляем фишки
                        const {x, y} = data.botPosition;
                        botChip.style.top = (y + 70) + 'px';
                        botChip.style.left = (x + 30) + 'px';

                        // Если бот победил
                        if (data.winMessage) {
                            alert(data.winMessage);
                            gameOver = true;
                            showRestartButton();
                        } else {
                            // кнопка для следующего хода игрока
                            document.querySelector('button').style.display = 'inline-block';
                        }
                    }
                }

                moveToNextStep();
            })
            .catch(error => console.error('Ошибка:', error));
    }


</script>

</body>
</html>
", "field/index.html.twig", "C:\\Users\\gwelk\\PhpstormProjects\\walking_game\\game_project\\templates\\field\\index.html.twig");
    }
}
