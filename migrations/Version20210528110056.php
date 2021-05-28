<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20210528110056 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE echantillon ADD table_jury_id INT DEFAULT NULL, CHANGE procede_id procede_id INT NOT NULL');
        $this->addSql('ALTER TABLE echantillon ADD CONSTRAINT FK_2C649BE7E7535133 FOREIGN KEY (table_jury_id) REFERENCES `table` (id)');
        $this->addSql('CREATE INDEX IDX_2C649BE7E7535133 ON echantillon (table_jury_id)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE echantillon DROP FOREIGN KEY FK_2C649BE7E7535133');
        $this->addSql('DROP INDEX IDX_2C649BE7E7535133 ON echantillon');
        $this->addSql('ALTER TABLE echantillon DROP table_jury_id, CHANGE procede_id procede_id INT DEFAULT NULL');
    }
}
