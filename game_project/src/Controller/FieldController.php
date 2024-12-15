<?php

namespace App\Controller;

use App\DataFixtures\PlayerFixtures;
use App\Entity\Player;
use App\Entity\Field;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class FieldController extends AbstractController
{
    #[Route('/field', name: 'field')]
    public function index(EntityManagerInterface $entityManager): Response
    {
        // Получаем всех игроков
        $players = $entityManager->getRepository(Player::class)->findAll();

        // Проверяем, есть ли игроки
        if (!$players) {
            throw $this->createNotFoundException('Игроки не найдены');
        }

        // Координаты игроков
        $coordinates = [];

        foreach ($players as $player) {
            // Получаем поле, на котором находится игрок
            $currentField = $entityManager->getRepository(Field::class)->find($player->getCurrentCell());

            if ($currentField) {
                $coordinates[] = [
                    'id' => $currentField->getId(),
                    'x' => $currentField->getX(),
                    'y' => $currentField->getY(),
                    'isBot' => $player->isBot(),
                ];
            }
        }

        // Передаем данные в шаблон
        return $this->render('field/index.html.twig', [
            'coordinates' => $coordinates,
        ]);
    }

    #[Route('/update-player-position', name: 'update_player_position', methods: ['POST'])]
    public function updatePlayerPosition(Request $request, EntityManagerInterface $entityManager): JsonResponse
    {
        $data = json_decode($request->getContent(), true);
        $diceResult = $data['diceResult'] ?? 0;

        $player = $entityManager->getRepository(Player::class)->findOneBy(['isBot' => false]);
        if (!$player) {
            return new JsonResponse(['error' => 'Player not found'], Response::HTTP_NOT_FOUND);
        }

        $currentCellId = $player->getCurrentCell();
        $newCellId = $currentCellId + $diceResult;

        if ($newCellId >= 36) {
            $newCellId = 36; // Игрок фиксируется на клетке 36
        }

        // Генерация списка шагов
        $steps = [];
        for ($i = $currentCellId + 1; $i <= $newCellId; $i++) {
            $field = $entityManager->getRepository(Field::class)->find($i);
            $steps[] = [
                'x' => $field->getX(),
                'y' => $field->getY(),
            ];
        }

        // Перемещаем игрока на новое поле
        $player->setCurrentCell($newCellId);
        $entityManager->flush();  // Обновляем позицию игрока

        // Проверка на специальные поля
        $activeFields = [12, 23, 29, 35];
        if (in_array($newCellId, $activeFields)) {
            // Если попали на специальное поле, откидываем на 3 клетки назад
            $newCellId -= 3;
            $player->setCurrentCell($newCellId);
            $entityManager->flush();  // Обновляем позицию игрока после откидывания
        }

        // Дополнительные специальные поля с бонусами
        $activeFields1 = [7, 27];
        if (in_array($newCellId, $activeFields1)) {
            // Если попали на специальное поле, двигаем на 4 клетки вперед
            $newCellId += 4;
            $player->setCurrentCell($newCellId);
            $entityManager->flush();  // Обновляем позицию игрока после перемещения вперед
        }

        $activeFields2 = [14];
        if (in_array($newCellId, $activeFields2)) {
            // Если попали на специальное поле, переносим на клетку 22
            $newCellId = 22;
            $player->setCurrentCell($newCellId);
            $entityManager->flush();  // Обновляем позицию игрока после перемещения
        }

        // Добавление случайных действий на клетки 5, 11, 16, 25, 32
        $specialFields = [5, 11, 16, 25, 32];
        if (in_array($newCellId, $specialFields)) {
            $action = rand(1, 4); // Выбираем одно из 4 возможных действий случайным образом

            switch ($action) {
                case 1:
                    // Игрок перемещается на 4 клетки вперед
                    $newCellId += 4;
                    break;
                case 2:
                    // Игрок перемещается на 4 клетки назад
                    $newCellId -= 4;
                    break;
                case 3:
                    // Противник перемещается на 4 клетки вперед
                    $opponent = $entityManager->getRepository(Player::class)->findOneBy(['isBot' => true]);
                    if ($opponent) {
                        $opponentNewCellId = $opponent->getCurrentCell() + 4;
                        if ($opponentNewCellId < 36) {
                            $opponent->setCurrentCell($opponentNewCellId);
                            $entityManager->flush();
                        }
                    }
                    break;
                case 4:
                    // Противник перемещается на 4 клетки назад
                    $opponent = $entityManager->getRepository(Player::class)->findOneBy(['isBot' => true]);
                    if ($opponent) {
                        $opponentNewCellId = $opponent->getCurrentCell() - 4;
                        if ($opponentNewCellId > 0) {
                            $opponent->setCurrentCell($opponentNewCellId);
                            $entityManager->flush();
                        }
                    }
                    break;
            }

            // После выполнения действия, обновляем позицию игрока
            $player->setCurrentCell($newCellId);
            $entityManager->flush(); // Обновляем позицию игрока
        }

        $newField = $entityManager->getRepository(Field::class)->find($newCellId);

        $winMessage = '';
        if ($newCellId === 36) {
            $winMessage = 'Вы победили!';
        }

        return new JsonResponse([
            'steps' => $steps,
            'winMessage' => $winMessage,
            'playerPosition' => [
                'x' => $newField->getX(),
                'y' => $newField->getY(),
            ],
        ]);
    }






    #[Route('/reset-game', name: 'reset_game', methods: ['POST'])]
    public function resetGame(EntityManagerInterface $entityManager): JsonResponse
    {
        // Находим всех игроков (реальных и ботов)
        $players = $entityManager->getRepository(Player::class)->findAll();

        foreach ($players as $player) {
            // Сброс позиции игрока на начальную клетку (например, клетка с ID 1)
            $player->setCurrentCell(1);  // Предполагаем, что начальная клетка - клетка с ID 1
        }

        // Сохраняем изменения в базе данных
        $entityManager->flush();

        return new JsonResponse(['message' => 'Игра сброшена, начните заново']);
    }

    #[Route('/move-bot', name: 'move_bot', methods: ['POST'])]
    public function moveBot(EntityManagerInterface $entityManager): JsonResponse
    {
        // Находим бота
        $bot = $entityManager->getRepository(Player::class)->findOneBy(['isBot' => true]);
        if (!$bot) {
            return new JsonResponse(['error' => 'Bot not found'], Response::HTTP_NOT_FOUND);
        }

        // Генерация случайного хода для бота (бросок кубиков)
        $diceResult = rand(1, 6) + rand(1, 6); // Бот кидает два кубика

        // Перемещаем бота
        $currentCell = $bot->getCurrentCell();
        $newCellId = $currentCell + $diceResult;

        // Проверка на активные поля
        $activeFields = [12, 23, 29, 35];
        if (in_array($newCellId, $activeFields)) {
            $newCellId -= 3; // Возвращаем игрока на 3 клетки назад
        }

        // Если новая клетка больше 36, фиксируем бота на клетке 36
        if ($newCellId >= 36) {
            $newCellId = 36;
        }

        // Генерация списка шагов
        $steps = [];
        for ($i = $currentCell + 1; $i <= $newCellId; $i++) {
            $field = $entityManager->getRepository(Field::class)->find($i);
            $steps[] = [
                'x' => $field->getX(),
                'y' => $field->getY(),
            ];
        }

        // Проверка для специальных полей 5, 11, 16, 25, 32
        $specialFields = [5, 11, 16, 25, 32];
        if (in_array($newCellId, $specialFields)) {
            $action = rand(1, 4); // Выбираем одно из 4 возможных действий случайным образом

            switch ($action) {
                case 1:
                    // Бот перемещается на 4 клетки вперед
                    $newCellId += 4;
                    break;
                case 2:
                    // Бот перемещается на 4 клетки назад
                    $newCellId -= 4;
                    break;
                case 3:
                    // Противник (игрок) перемещается на 4 клетки вперед
                    $player = $entityManager->getRepository(Player::class)->findOneBy(['isBot' => false]);
                    if ($player) {
                        $playerNewCellId = $player->getCurrentCell() + 4;
                        if ($playerNewCellId < 36) {
                            $player->setCurrentCell($playerNewCellId);
                            $entityManager->flush();
                        }
                    }
                    break;
                case 4:
                    // Противник (игрок) перемещается на 4 клетки назад
                    $player = $entityManager->getRepository(Player::class)->findOneBy(['isBot' => false]);
                    if ($player) {
                        $playerNewCellId = $player->getCurrentCell() - 4;
                        if ($playerNewCellId > 0) {
                            $player->setCurrentCell($playerNewCellId);
                            $entityManager->flush();
                        }
                    }
                    break;
            }

            // После выполнения действия, обновляем позицию бота
            $bot->setCurrentCell($newCellId);
            $entityManager->flush(); // Обновляем позицию бота
        } else {
            // Если бот не попал на специальное поле, сразу обновляем его координаты
            $bot->setCurrentCell($newCellId);
            $entityManager->flush();
        }

        // Обновляем клетку бота
        $bot->setCurrentCell($newCellId);
        $entityManager->flush();

        // Проверка победы бота
        if ($newCellId === 36) {
            return new JsonResponse([
                'winMessage' => 'Бот победил!',
                'steps' => $steps,
                'botPosition' => ['x' => $steps[count($steps) - 1]['x'], 'y' => $steps[count($steps) - 1]['y']],
            ]);
        }

        // Если бот не победил, просто возвращаем шаги
        return new JsonResponse([
            'steps' => $steps,
            'botPosition' => ['x' => $steps[count($steps) - 1]['x'], 'y' => $steps[count($steps) - 1]['y']],
        ]);
    }















    /*#[Route('/save-coordinates', name: 'save_coordinates', methods: ['POST'])]
    public function saveCoordinates(Request $request, EntityManagerInterface $entityManager): JsonResponse
    {
        // Получаем данные из запроса
        $data = json_decode($request->getContent(), true);
        $x = $data['x'];
        $y = $data['y'];

        // Создаём новый объект Field и сохраняем координаты
        $field = new Field();
        $field->setX($x);
        $field->setY($y);

        $entityManager->persist($field);
        $entityManager->flush();

        return new JsonResponse(['message' => 'Координаты сохранены', 'status' => 'success']);
    }*/
}