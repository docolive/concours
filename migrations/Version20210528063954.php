<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20210528063954 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // // this up() migration is auto-generated, please modify it to your needs
        // $this->addSql('CREATE TABLE `table` (id INT AUTO_INCREMENT NOT NULL, categorie_id INT DEFAULT NULL, name VARCHAR(255) NOT NULL, max_echs INT NOT NULL, INDEX IDX_F6298F46BCF5E72D (categorie_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        // $this->addSql('ALTER TABLE `table` ADD CONSTRAINT FK_F6298F46BCF5E72D FOREIGN KEY (categorie_id) REFERENCES categorie (id)');
        //$this->addSql('ALTER TABLE echantillon CHANGE procede_id procede_id INT NOT NULL');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP TABLE `table`');
        $this->addSql('ALTER TABLE echantillon CHANGE procede_id procede_id INT DEFAULT NULL');
    }
}
