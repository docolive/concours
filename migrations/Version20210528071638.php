<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20210528071638 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // // this up() migration is auto-generated, please modify it to your needs
        // $this->addSql('ALTER TABLE categorie ADD concours_id INT DEFAULT NULL');
        // $this->addSql('ALTER TABLE categorie ADD CONSTRAINT FK_497DD634D11E3C7 FOREIGN KEY (concours_id) REFERENCES concours (id)');
        // $this->addSql('CREATE INDEX IDX_497DD634D11E3C7 ON categorie (concours_id)');
        // $this->addSql('ALTER TABLE echantillon CHANGE procede_id procede_id INT NOT NULL');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE categorie DROP FOREIGN KEY FK_497DD634D11E3C7');
        $this->addSql('DROP INDEX IDX_497DD634D11E3C7 ON categorie');
        $this->addSql('ALTER TABLE categorie DROP concours_id');
        $this->addSql('ALTER TABLE echantillon CHANGE procede_id procede_id INT DEFAULT NULL');
    }
}
