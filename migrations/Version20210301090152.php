<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20210301090152 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE procede (id INT AUTO_INCREMENT NOT NULL, categorie_id INT DEFAULT NULL, name VARCHAR(255) NOT NULL, INDEX IDX_774A2951BCF5E72D (categorie_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE procede ADD CONSTRAINT FK_774A2951BCF5E72D FOREIGN KEY (categorie_id) REFERENCES categorie (id)');
        $this->addSql('ALTER TABLE echantillon ADD procede_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE echantillon ADD CONSTRAINT FK_2C649BE7D970B6A5 FOREIGN KEY (procede_id) REFERENCES procede (id)');
        $this->addSql('CREATE INDEX IDX_2C649BE7D970B6A5 ON echantillon (procede_id)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE echantillon DROP FOREIGN KEY FK_2C649BE7D970B6A5');
        $this->addSql('DROP TABLE procede');
        $this->addSql('DROP INDEX IDX_2C649BE7D970B6A5 ON echantillon');
        $this->addSql('ALTER TABLE echantillon DROP procede_id');
    }
}
