<?php

namespace App\Entity;

use App\Repository\PlayerRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: PlayerRepository::class)]
class Player
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $name = null;

    #[ORM\Column]
    private ?bool $isBot = null;

    #[ORM\Column]
    private ?int $currentCell = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): static
    {
        $this->name = $name;

        return $this;
    }

    public function isBot(): ?bool
    {
        return $this->isBot;
    }

    public function setBot(bool $isBot): static
    {
        $this->isBot = $isBot;

        return $this;
    }

    public function getCurrentCell(): ?int
    {
        return $this->currentCell;
    }

    public function setCurrentCell(int $currentCell): static
    {
        $this->currentCell = $currentCell;

        return $this;
    }
}
