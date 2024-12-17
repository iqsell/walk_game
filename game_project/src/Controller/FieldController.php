<?php

namespace App\Controller;

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
    private function movePlayer(Player $player, int $diceResult, EntityManagerInterface $entityManager): array
    {
        $currentCellId = $player->getCurrentCell();
        $newCellId = $currentCellId + $diceResult;

        if ($newCellId >= 36) {
            $newCellId = 36; // финиш
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

        // Обработка спец. полей
        $activeFields = [12, 23, 29, 35];
        if (in_array($newCellId, $activeFields)) {
            // Если попали на спец поле, откидываем на 3 клетки назад
            $newCellId -= 3;
        }

        $specialFields = [5, 11, 16, 25, 32];
        if (in_array($newCellId, $specialFields)) {
            $action = rand(1, 4); // шанс 1 из 4

            switch ($action) {
                case 1:
                    $newCellId += 4;
                    break;
                case 2:
                    $newCellId -= 4;
                    break;
                case 3:
                    // Противник перемещается на 4 клетки вперед
                    $opponent = $entityManager->getRepository(Player::class)->findOneBy(['isBot' => !$player->isBot()]);
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
                    $opponent = $entityManager->getRepository(Player::class)->findOneBy(['isBot' => !$player->isBot()]);
                    if ($opponent) {
                        $opponentNewCellId = $opponent->getCurrentCell() - 4;
                        if ($opponentNewCellId > 0) {
                            $opponent->setCurrentCell($opponentNewCellId);
                            $entityManager->flush();
                        }
                    }
                    break;
            }
        }

        // Обновляем позицию игрока/бота
        $player->setCurrentCell($newCellId);
        $entityManager->flush();

        $newField = $entityManager->getRepository(Field::class)->find($newCellId);
        return [
            'steps' => $steps,
            'newPosition' => [
                'x' => $newField->getX(),
                'y' => $newField->getY(),
            ],
            'winMessage' => $newCellId === 36 ? 'Вы победили!' : '',
        ];
    }

    #[Route('/field', name: 'field')]
    public function index(EntityManagerInterface $entityManager): Response
    {
        // Получаем всех игроков
        $players = $entityManager->getRepository(Player::class)->findAll();

        // есть ли игроки
        if (!$players) {
            throw $this->createNotFoundException('Игроки не найдены');
        }

        // Координаты игроков
        $coordinates = [];

        foreach ($players as $player) {
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

        $result = $this->movePlayer($player, $diceResult, $entityManager);

        return new JsonResponse([
            'steps' => $result['steps'],
            'winMessage' => $result['winMessage'],
            'playerPosition' => $result['newPosition'],
        ]);
    }

    #[Route('/move-bot', name: 'move_bot', methods: ['POST'])]
    public function moveBot(EntityManagerInterface $entityManager): JsonResponse
    {
        $bot = $entityManager->getRepository(Player::class)->findOneBy(['isBot' => true]);
        if (!$bot) {
            return new JsonResponse(['error' => 'Bot not found'], Response::HTTP_NOT_FOUND);
        }

        // Генерация случайного хода для бота
        $diceResult = rand(1, 6) + rand(1, 6);

        $result = $this->movePlayer($bot, $diceResult, $entityManager);

        return new JsonResponse([
            'steps' => $result['steps'],
            'winMessage' => $result['winMessage'],
            'botPosition' => $result['newPosition'],
        ]);
    }

    #[Route('/reset-game', name: 'reset_game', methods: ['POST'])]
    public function resetGame(EntityManagerInterface $entityManager): JsonResponse
    {
        // все игроки
        $players = $entityManager->getRepository(Player::class)->findAll();

        foreach ($players as $player) {
            // Сброс позиции игрока на начальную клетку (например, клетка с ID 1)
            $player->setCurrentCell(1);  // Предполагаем, что начальная клетка - клетка с ID 1
        }

        // Сохраняем изменения в базе данных
        $entityManager->flush();

        return new JsonResponse(['message' => 'Игра сброшена, начните заново']);
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
