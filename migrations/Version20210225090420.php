<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20210225090420 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE paiement (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE echantillon ADD paiement_id INT DEFAULT NULL, ADD medaille_id INT DEFAULT NULL, ADD paye TINYINT(1) NOT NULL, ADD recu TINYINT(1) NOT NULL, ADD observation LONGTEXT DEFAULT NULL');
        $this->addSql('ALTER TABLE echantillon ADD CONSTRAINT FK_2C649BE72A4C4478 FOREIGN KEY (paiement_id) REFERENCES paiement (id)');
        $this->addSql('ALTER TABLE echantillon ADD CONSTRAINT FK_2C649BE772E59222 FOREIGN KEY (medaille_id) REFERENCES medaille (id)');
        $this->addSql('CREATE INDEX IDX_2C649BE72A4C4478 ON echantillon (paiement_id)');
        $this->addSql('CREATE INDEX IDX_2C649BE772E59222 ON echantillon (medaille_id)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE echantillon DROP FOREIGN KEY FK_2C649BE72A4C4478');
        $this->addSql('DROP TABLE paiement');
        $this->addSql('ALTER TABLE echantillon DROP FOREIGN KEY FK_2C649BE772E59222');
        $this->addSql('DROP INDEX IDX_2C649BE72A4C4478 ON echantillon');
        $this->addSql('DROP INDEX IDX_2C649BE772E59222 ON echantillon');
        $this->addSql('ALTER TABLE echantillon DROP paiement_id, DROP medaille_id, DROP paye, DROP recu, DROP observation');
    }
}
