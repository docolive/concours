<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20210218005758 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE type ADD concours_id INT NOT NULL, ADD unite VARCHAR(2) NOT NULL');
        $this->addSql('ALTER TABLE type ADD CONSTRAINT FK_8CDE5729D11E3C7 FOREIGN KEY (concours_id) REFERENCES concours (id)');
        $this->addSql('CREATE INDEX IDX_8CDE5729D11E3C7 ON type (concours_id)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE type DROP FOREIGN KEY FK_8CDE5729D11E3C7');
        $this->addSql('DROP INDEX IDX_8CDE5729D11E3C7 ON type');
        $this->addSql('ALTER TABLE type DROP concours_id, DROP unite');
    }
}
