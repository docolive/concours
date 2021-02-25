<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20210225095822 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE echantillon ADD livraison_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE echantillon ADD CONSTRAINT FK_2C649BE78E54FB25 FOREIGN KEY (livraison_id) REFERENCES livraison (id)');
        $this->addSql('CREATE INDEX IDX_2C649BE78E54FB25 ON echantillon (livraison_id)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE echantillon DROP FOREIGN KEY FK_2C649BE78E54FB25');
        $this->addSql('DROP INDEX IDX_2C649BE78E54FB25 ON echantillon');
        $this->addSql('ALTER TABLE echantillon DROP livraison_id');
    }
}
