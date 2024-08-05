<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240805125714 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE expense DROP FOREIGN KEY FK_2D3A8DA6BC272CD1');
        $this->addSql('ALTER TABLE expense DROP FOREIGN KEY FK_2D3A8DA6A76ED395');
        $this->addSql('DROP INDEX IDX_2D3A8DA6A76ED395 ON expense');
        $this->addSql('DROP INDEX IDX_2D3A8DA6BC272CD1 ON expense');
        $this->addSql('ALTER TABLE expense DROP user_id, DROP associated_user_id');
        $this->addSql('ALTER TABLE user DROP expense_total, CHANGE username username VARCHAR(180) DEFAULT NULL, CHANGE roles roles JSON NOT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE expense ADD user_id INT NOT NULL, ADD associated_user_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE expense ADD CONSTRAINT FK_2D3A8DA6BC272CD1 FOREIGN KEY (associated_user_id) REFERENCES user (id) ON UPDATE NO ACTION ON DELETE NO ACTION');
        $this->addSql('ALTER TABLE expense ADD CONSTRAINT FK_2D3A8DA6A76ED395 FOREIGN KEY (user_id) REFERENCES user (id) ON UPDATE NO ACTION ON DELETE NO ACTION');
        $this->addSql('CREATE INDEX IDX_2D3A8DA6A76ED395 ON expense (user_id)');
        $this->addSql('CREATE INDEX IDX_2D3A8DA6BC272CD1 ON expense (associated_user_id)');
        $this->addSql('ALTER TABLE user ADD expense_total DOUBLE PRECISION DEFAULT NULL, CHANGE username username VARCHAR(180) NOT NULL, CHANGE roles roles JSON NOT NULL COLLATE `utf8mb4_bin`');
    }
}
