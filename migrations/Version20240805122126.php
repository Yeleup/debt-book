<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240805122126 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE employee DROP FOREIGN KEY FK_5D9F75A132C8A3DE');
        $this->addSql('ALTER TABLE employee ADD CONSTRAINT FK_5D9F75A132C8A3DE FOREIGN KEY (organization_id) REFERENCES organization (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE expense ADD employee_id INT DEFAULT NULL, ADD organization_id INT DEFAULT NULL, ADD reference VARCHAR(255) NOT NULL, CHANGE created_at created_at DATETIME DEFAULT NULL');
        $this->addSql('ALTER TABLE expense ADD CONSTRAINT FK_2D3A8DA68C03F15C FOREIGN KEY (employee_id) REFERENCES employee (id)');
        $this->addSql('ALTER TABLE expense ADD CONSTRAINT FK_2D3A8DA632C8A3DE FOREIGN KEY (organization_id) REFERENCES organization (id)');
        $this->addSql('CREATE INDEX IDX_2D3A8DA68C03F15C ON expense (employee_id)');
        $this->addSql('CREATE INDEX IDX_2D3A8DA632C8A3DE ON expense (organization_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE expense DROP FOREIGN KEY FK_2D3A8DA68C03F15C');
        $this->addSql('ALTER TABLE expense DROP FOREIGN KEY FK_2D3A8DA632C8A3DE');
        $this->addSql('DROP INDEX IDX_2D3A8DA68C03F15C ON expense');
        $this->addSql('DROP INDEX IDX_2D3A8DA632C8A3DE ON expense');
        $this->addSql('ALTER TABLE expense DROP employee_id, DROP organization_id, DROP reference, CHANGE created_at created_at DATETIME NOT NULL');
        $this->addSql('ALTER TABLE employee DROP FOREIGN KEY FK_5D9F75A132C8A3DE');
        $this->addSql('ALTER TABLE employee ADD CONSTRAINT FK_5D9F75A132C8A3DE FOREIGN KEY (organization_id) REFERENCES organization (id) ON UPDATE NO ACTION ON DELETE NO ACTION');
    }
}
