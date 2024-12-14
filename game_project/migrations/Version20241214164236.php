<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20241214164236 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TEMPORARY TABLE __temp__field AS SELECT num, coord FROM field');
        $this->addSql('DROP TABLE field');
        $this->addSql('CREATE TABLE field (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, coord VARCHAR(255) NOT NULL)');
        $this->addSql('INSERT INTO field (id, coord) SELECT num, coord FROM __temp__field');
        $this->addSql('DROP TABLE __temp__field');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TEMPORARY TABLE __temp__field AS SELECT id, coord FROM field');
        $this->addSql('DROP TABLE field');
        $this->addSql('CREATE TABLE field (num INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, coord VARCHAR(255) NOT NULL)');
        $this->addSql('INSERT INTO field (num, coord) SELECT id, coord FROM __temp__field');
        $this->addSql('DROP TABLE __temp__field');
    }
}
