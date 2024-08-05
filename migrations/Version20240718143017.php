<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240718143017 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE transfer (id INT AUTO_INCREMENT NOT NULL, organization_id INT NOT NULL, employee_id INT NOT NULL, receiver_employee_id INT NOT NULL, amount DOUBLE PRECISION NOT NULL, comment VARCHAR(255) DEFAULT NULL, created_at DATETIME DEFAULT NULL, updated_at DATETIME DEFAULT NULL, reference VARCHAR(255) NOT NULL, INDEX IDX_4034A3C032C8A3DE (organization_id), INDEX IDX_4034A3C08C03F15C (employee_id), INDEX IDX_4034A3C06EF82F4E (receiver_employee_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE transfer ADD CONSTRAINT FK_4034A3C032C8A3DE FOREIGN KEY (organization_id) REFERENCES organization (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE transfer ADD CONSTRAINT FK_4034A3C08C03F15C FOREIGN KEY (employee_id) REFERENCES employee (id)');
        $this->addSql('ALTER TABLE transfer ADD CONSTRAINT FK_4034A3C06EF82F4E FOREIGN KEY (receiver_employee_id) REFERENCES employee (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE transfer DROP FOREIGN KEY FK_4034A3C032C8A3DE');
        $this->addSql('ALTER TABLE transfer DROP FOREIGN KEY FK_4034A3C08C03F15C');
        $this->addSql('ALTER TABLE transfer DROP FOREIGN KEY FK_4034A3C06EF82F4E');
        $this->addSql('DROP TABLE transfer');
    }
}
