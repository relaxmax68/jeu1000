<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20190614163156 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE step (id INT AUTO_INCREMENT NOT NULL, question_id INT DEFAULT NULL, suite INT NOT NULL, INDEX IDX_43B9FE3C1E27F6BF (question_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE jeu (id INT AUTO_INCREMENT NOT NULL, date DATETIME NOT NULL, score INT NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE jeu_player (jeu_id INT NOT NULL, player_id INT NOT NULL, INDEX IDX_2B4DE5488C9E392E (jeu_id), INDEX IDX_2B4DE54899E6F5DF (player_id), PRIMARY KEY(jeu_id, player_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE step ADD CONSTRAINT FK_43B9FE3C1E27F6BF FOREIGN KEY (question_id) REFERENCES question (id)');
        $this->addSql('ALTER TABLE jeu_player ADD CONSTRAINT FK_2B4DE5488C9E392E FOREIGN KEY (jeu_id) REFERENCES jeu (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE jeu_player ADD CONSTRAINT FK_2B4DE54899E6F5DF FOREIGN KEY (player_id) REFERENCES player (id) ON DELETE CASCADE');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE jeu_player DROP FOREIGN KEY FK_2B4DE5488C9E392E');
        $this->addSql('DROP TABLE step');
        $this->addSql('DROP TABLE jeu');
        $this->addSql('DROP TABLE jeu_player');
    }
}
