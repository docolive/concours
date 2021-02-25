<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20210225051233 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE echantillon_livraison (echantillon_id INT NOT NULL, livraison_id INT NOT NULL, INDEX IDX_2AD209401286F5A9 (echantillon_id), INDEX IDX_2AD209408E54FB25 (livraison_id), PRIMARY KEY(echantillon_id, livraison_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE echantillon_livraison ADD CONSTRAINT FK_2AD209401286F5A9 FOREIGN KEY (echantillon_id) REFERENCES echantillon (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE echantillon_livraison ADD CONSTRAINT FK_2AD209408E54FB25 FOREIGN KEY (livraison_id) REFERENCES livraison (id) ON DELETE CASCADE');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP TABLE echantillon_livraison');
    }
}
