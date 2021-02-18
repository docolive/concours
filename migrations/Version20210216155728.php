<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20210216155728 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE categorie (id INT AUTO_INCREMENT NOT NULL, concours_id INT NOT NULL, type_id INT NOT NULL, ordre INT NOT NULL, name VARCHAR(255) NOT NULL, INDEX IDX_497DD634D11E3C7 (concours_id), INDEX IDX_497DD634C54C8C93 (type_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE concours (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, date DATETIME DEFAULT NULL, lieu VARCHAR(255) DEFAULT NULL, debut_inscription DATE DEFAULT NULL, fin_inscription DATE DEFAULT NULL, cout NUMERIC(5, 2) DEFAULT NULL, tva NUMERIC(4, 2) DEFAULT NULL, resp_name VARCHAR(255) DEFAULT NULL, resp_adress1 VARCHAR(255) DEFAULT NULL, resp_adress2 VARCHAR(255) DEFAULT NULL, resp_adress3 VARCHAR(255) DEFAULT NULL, resp_phone VARCHAR(255) DEFAULT NULL, resp_email VARCHAR(255) DEFAULT NULL, couv_palmares VARCHAR(255) DEFAULT NULL, adress1 VARCHAR(255) DEFAULT NULL, adress2 VARCHAR(255) DEFAULT NULL, adress3 VARCHAR(255) DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE echantillon (id INT AUTO_INCREMENT NOT NULL, categorie_id INT NOT NULL, description VARCHAR(255) DEFAULT NULL, lot VARCHAR(255) NOT NULL, volume INT NOT NULL, public_ref VARCHAR(255) NOT NULL, code VARCHAR(255) DEFAULT NULL, INDEX IDX_2C649BE7BCF5E72D (categorie_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE medaille (id INT AUTO_INCREMENT NOT NULL, nom VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE profil (id INT AUTO_INCREMENT NOT NULL, user_id INT DEFAULT NULL, raison_sociale VARCHAR(255) DEFAULT NULL, nom VARCHAR(255) DEFAULT NULL, prenom VARCHAR(255) DEFAULT NULL, adress1 VARCHAR(255) DEFAULT NULL, adress2 VARCHAR(255) DEFAULT NULL, adress3 VARCHAR(255) DEFAULT NULL, adress4 VARCHAR(255) DEFAULT NULL, adress5 VARCHAR(255) DEFAULT NULL, phone VARCHAR(255) DEFAULT NULL, UNIQUE INDEX UNIQ_E6D6B297A76ED395 (user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE type (id INT AUTO_INCREMENT NOT NULL, ordre INT DEFAULT NULL, nom VARCHAR(255) NOT NULL, vol_min_lot INT NOT NULL, nbre_max_ech INT NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE categorie ADD CONSTRAINT FK_497DD634D11E3C7 FOREIGN KEY (concours_id) REFERENCES concours (id)');
        $this->addSql('ALTER TABLE categorie ADD CONSTRAINT FK_497DD634C54C8C93 FOREIGN KEY (type_id) REFERENCES type (id)');
        $this->addSql('ALTER TABLE echantillon ADD CONSTRAINT FK_2C649BE7BCF5E72D FOREIGN KEY (categorie_id) REFERENCES categorie (id)');
        $this->addSql('ALTER TABLE profil ADD CONSTRAINT FK_E6D6B297A76ED395 FOREIGN KEY (user_id) REFERENCES `user` (id)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE echantillon DROP FOREIGN KEY FK_2C649BE7BCF5E72D');
        $this->addSql('ALTER TABLE categorie DROP FOREIGN KEY FK_497DD634D11E3C7');
        $this->addSql('ALTER TABLE categorie DROP FOREIGN KEY FK_497DD634C54C8C93');
        $this->addSql('DROP TABLE categorie');
        $this->addSql('DROP TABLE concours');
        $this->addSql('DROP TABLE echantillon');
        $this->addSql('DROP TABLE medaille');
        $this->addSql('DROP TABLE profil');
        $this->addSql('DROP TABLE type');
    }
}
