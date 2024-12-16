<?php

namespace App\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class FieldControllerTest extends WebTestCase
{
    public function testRollDice(): void
    {
        $client = static::createClient();

        // Делаем запрос на страницу игры
        $crawler = $client->request('GET', '/field');

        // Проверяем, что страница загружается
        $this->assertResponseIsSuccessful();
        $this->assertSelectorTextContains('button', 'Бросить кубики');

        // Симулируем бросок кубиков
        $diceResult = 7; // Примерное значение суммы кубиков
        $client->jsonRequest('POST', '/update-player-position', [
            'diceResult' => $diceResult
        ]);

        // Проверяем, что запрос был успешным
        $this->assertResponseIsSuccessful();

        // Проверяем содержимое ответа (например, обновлённые координаты или сообщение)
        $responseContent = $client->getResponse()->getContent();
        $this->assertStringContainsString('steps', $responseContent);

        // Дополнительно проверяем логику победы или следующего хода
        $data = json_decode($responseContent, true);
        $this->assertArrayHasKey('steps', $data);
        $this->assertIsArray($data['steps']);
        if (isset($data['winMessage'])) {
            $this->assertIsString($data['winMessage']);
        }
    }

    public function testMoveBot(): void
    {
        $client = static::createClient();

        // Симулируем ход бота
        $client->jsonRequest('POST', '/move-bot');

        // Проверяем, что запрос был успешным
        $this->assertResponseIsSuccessful();

        // Проверяем содержимое ответа
        $responseContent = $client->getResponse()->getContent();
        $this->assertStringContainsString('steps', $responseContent);

        // Проверяем данные ответа
        $data = json_decode($responseContent, true);
        $this->assertArrayHasKey('steps', $data);
        $this->assertIsArray($data['steps']);
        if (isset($data['winMessage'])) {
            $this->assertIsString($data['winMessage']);
        }
    }

    public function testResetGame(): void
    {
        $client = static::createClient();

        // Симулируем сброс игры
        $client->jsonRequest('POST', '/reset-game');

        // Проверяем, что запрос был успешным
        $this->assertResponseIsSuccessful();

        // Проверяем содержимое ответа
        $responseContent = $client->getResponse()->getContent();
        $this->assertStringContainsString('message', $responseContent);

        // Проверяем данные ответа
        $data = json_decode($responseContent, true);
        $this->assertArrayHasKey('message', $data);
        $this->assertEquals('Игра сброшена, начните заново', $data['message']);
    }
}
