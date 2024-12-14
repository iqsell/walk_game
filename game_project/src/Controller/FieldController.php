<?php

namespace App\Controller;

use App\Entity\Field;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class FieldController extends AbstractController
{
    #[Route('/field', name: 'field')]
    public function index(EntityManagerInterface $entityManager): Response
    {
        // Получаем текущее поле (например, начальное поле с id = 1)
        $currentFieldId = 1;  // Пример текущего поля, начальная позиция
        $currentField = $entityManager->getRepository(Field::class)->find($currentFieldId);

        // Если текущее поле найдено, получаем координаты
        $coordinates = [];
        if ($currentField) {
            $coordinates[] = [
                'id' => $currentField->getId(),
                'x' => $currentField->getX(),
                'y' => $currentField->getY(),
            ];
        }

        // Передаем данные в шаблон
        return $this->render('field/index.html.twig', [
            'coordinates' => $coordinates,
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