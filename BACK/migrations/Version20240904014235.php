<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240904014235 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE distribution (id INT AUTO_INCREMENT NOT NULL, date DATETIME NOT NULL, lieu VARCHAR(255) NOT NULL, capacite_maximale INT NOT NULL, nombre_inscrits INT NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci`');
        $this->addSql('CREATE TABLE inscription_distribution (id INT AUTO_INCREMENT NOT NULL, nom_participant VARCHAR(255) NOT NULL, email_participant VARCHAR(255) NOT NULL, telephone_participant VARCHAR(255) NOT NULL, distribution_id INT NOT NULL, INDEX IDX_226C94C86EB6DDB5 (distribution_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci`');
        $this->addSql('ALTER TABLE inscription_distribution ADD CONSTRAINT FK_226C94C86EB6DDB5 FOREIGN KEY (distribution_id) REFERENCES distribution (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE inscription_distribution DROP FOREIGN KEY FK_226C94C86EB6DDB5');
        $this->addSql('DROP TABLE distribution');
        $this->addSql('DROP TABLE inscription_distribution');
    }
}
