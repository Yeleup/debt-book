<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240619154146 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE type ADD organization_id INT DEFAULT NULL, ADD is_add_amount TINYINT(1) NOT NULL, DROP prefix, DROP color');
        $this->addSql('ALTER TABLE type ADD CONSTRAINT FK_8CDE572932C8A3DE FOREIGN KEY (organization_id) REFERENCES organization (id)');
        $this->addSql('CREATE INDEX IDX_8CDE572932C8A3DE ON type (organization_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE type DROP FOREIGN KEY FK_8CDE572932C8A3DE');
        $this->addSql('DROP INDEX IDX_8CDE572932C8A3DE ON type');
        $this->addSql('ALTER TABLE type ADD prefix VARCHAR(3) DEFAULT NULL, ADD color VARCHAR(255) DEFAULT NULL, DROP organization_id, DROP is_add_amount');
    }
}
