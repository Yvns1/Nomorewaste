<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240903161658 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE benevoles_services (benevoles_id INT NOT NULL, services_id INT NOT NULL, INDEX IDX_FB45350F9E4A7EFD (benevoles_id), INDEX IDX_FB45350FAEF5A6C1 (services_id), PRIMARY KEY(benevoles_id, services_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci`');
        $this->addSql('ALTER TABLE benevoles_services ADD CONSTRAINT FK_FB45350F9E4A7EFD FOREIGN KEY (benevoles_id) REFERENCES benevoles (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE benevoles_services ADD CONSTRAINT FK_FB45350FAEF5A6C1 FOREIGN KEY (services_id) REFERENCES services (id) ON DELETE CASCADE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE benevoles_services DROP FOREIGN KEY FK_FB45350F9E4A7EFD');
        $this->addSql('ALTER TABLE benevoles_services DROP FOREIGN KEY FK_FB45350FAEF5A6C1');
        $this->addSql('DROP TABLE benevoles_services');
    }
}
