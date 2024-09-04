<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240904032548 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE inscription_tournee ADD user_id INT NOT NULL');
        $this->addSql('ALTER TABLE inscription_tournee ADD CONSTRAINT FK_C2278B92A76ED395 FOREIGN KEY (user_id) REFERENCES utilisateurs (id)');
        $this->addSql('CREATE INDEX IDX_C2278B92A76ED395 ON inscription_tournee (user_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE inscription_tournee DROP FOREIGN KEY FK_C2278B92A76ED395');
        $this->addSql('DROP INDEX IDX_C2278B92A76ED395 ON inscription_tournee');
        $this->addSql('ALTER TABLE inscription_tournee DROP user_id');
    }
}
