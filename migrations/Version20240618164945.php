<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240618164945 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE employee_market (employee_id INT NOT NULL, market_id INT NOT NULL, INDEX IDX_429D415E8C03F15C (employee_id), INDEX IDX_429D415E622F3F37 (market_id), PRIMARY KEY(employee_id, market_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE employee_market ADD CONSTRAINT FK_429D415E8C03F15C FOREIGN KEY (employee_id) REFERENCES employee (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE employee_market ADD CONSTRAINT FK_429D415E622F3F37 FOREIGN KEY (market_id) REFERENCES market (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE user_market DROP FOREIGN KEY FK_EFE9EF6622F3F37');
        $this->addSql('ALTER TABLE user_market DROP FOREIGN KEY FK_EFE9EF6A76ED395');
        $this->addSql('DROP TABLE user_market');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE user_market (user_id INT NOT NULL, market_id INT NOT NULL, INDEX IDX_EFE9EF6A76ED395 (user_id), INDEX IDX_EFE9EF6622F3F37 (market_id), PRIMARY KEY(user_id, market_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('ALTER TABLE user_market ADD CONSTRAINT FK_EFE9EF6622F3F37 FOREIGN KEY (market_id) REFERENCES market (id) ON UPDATE NO ACTION ON DELETE CASCADE');
        $this->addSql('ALTER TABLE user_market ADD CONSTRAINT FK_EFE9EF6A76ED395 FOREIGN KEY (user_id) REFERENCES user (id) ON UPDATE NO ACTION ON DELETE CASCADE');
        $this->addSql('ALTER TABLE employee_market DROP FOREIGN KEY FK_429D415E8C03F15C');
        $this->addSql('ALTER TABLE employee_market DROP FOREIGN KEY FK_429D415E622F3F37');
        $this->addSql('DROP TABLE employee_market');
    }
}
