<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240718150123 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE bank (id INT AUTO_INCREMENT NOT NULL, organization_id INT NOT NULL, employee_id INT NOT NULL, reference VARCHAR(255) NOT NULL, amount DOUBLE PRECISION NOT NULL, comment VARCHAR(255) DEFAULT NULL, created_at DATETIME DEFAULT NULL, updated_at DATETIME DEFAULT NULL, INDEX IDX_D860BF7A32C8A3DE (organization_id), INDEX IDX_D860BF7A8C03F15C (employee_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE bank ADD CONSTRAINT FK_D860BF7A32C8A3DE FOREIGN KEY (organization_id) REFERENCES organization (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE bank ADD CONSTRAINT FK_D860BF7A8C03F15C FOREIGN KEY (employee_id) REFERENCES employee (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE bank DROP FOREIGN KEY FK_D860BF7A32C8A3DE');
        $this->addSql('ALTER TABLE bank DROP FOREIGN KEY FK_D860BF7A8C03F15C');
        $this->addSql('DROP TABLE bank');
    }
}
