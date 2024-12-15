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
        // Получаем данные из запроса (результат броска кубиков)
        $data = json_decode($request->getContent(), true);
        $diceResult = $data['diceResult'] ?? 0;

        // Проверяем наличие игрока (реального)
        $player = $entityManager->getRepository(Player::class)->findOneBy(['isBot' => false]);
        if (!$player) {
            return new JsonResponse(['error' => 'Player not found'], Response::HTTP_NOT_FOUND);
        }

        // Перемещаем игрока
        $currentCellId = $player->getCurrentCell();
        $newCellId = $currentCellId + $diceResult;

        // Ограничиваем перемещение на клетке 36
        if ($newCellId >= 36) {
            $newCellId = 36; // Игрок фиксируется на клетке 36
        }

        // Обновляем клетку игрока
        $player->setCurrentCell($newCellId);
        $entityManager->flush();

        // Получаем новые координаты игрока
        $newField = $entityManager->getRepository(Field::class)->find($newCellId);

        // Проверка на победу
        $winMessage = '';
        if ($newCellId === 36) {
            $winMessage = 'Вы победили!';
        }

        return new JsonResponse([
            'newCoordinates' => [
                'id' => $newField->getId(),
                'x' => $newField->getX(),
                'y' => $newField->getY(),
            ],
            'winMessage' => $winMessage, // Отправляем сообщение о победе
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