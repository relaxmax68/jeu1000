<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20190808131507 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE jeu_player DROP FOREIGN KEY FK_2B4DE5488C9E392E');
        $this->addSql('ALTER TABLE step DROP FOREIGN KEY FK_43B9FE3C8C9E392E');
        $this->addSql('ALTER TABLE duo DROP FOREIGN KEY FK_3FDA4B94C0990423');
        $this->addSql('ALTER TABLE duo DROP FOREIGN KEY FK_3FDA4B94D22CABCD');
        $this->addSql('ALTER TABLE jeu_player DROP FOREIGN KEY FK_2B4DE54899E6F5DF');
        $this->addSql('DROP TABLE duo');
        $this->addSql('DROP TABLE jeu');
        $this->addSql('DROP TABLE jeu_player');
        $this->addSql('DROP TABLE player');
        $this->addSql('DROP TABLE step');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE duo (id INT AUTO_INCREMENT NOT NULL, player1_id INT DEFAULT NULL, player2_id INT DEFAULT NULL, UNIQUE INDEX UNIQ_3FDA4B94C0990423 (player1_id), UNIQUE INDEX UNIQ_3FDA4B94D22CABCD (player2_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('CREATE TABLE jeu (id INT AUTO_INCREMENT NOT NULL, date DATETIME NOT NULL, score INT NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('CREATE TABLE jeu_player (jeu_id INT NOT NULL, player_id INT NOT NULL, INDEX IDX_2B4DE54899E6F5DF (player_id), INDEX IDX_2B4DE5488C9E392E (jeu_id), PRIMARY KEY(jeu_id, player_id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('CREATE TABLE player (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL COLLATE utf8mb4_unicode_ci, score INT NOT NULL, available TINYINT(1) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('CREATE TABLE step (id INT AUTO_INCREMENT NOT NULL, question_id INT DEFAULT NULL, jeu_id INT NOT NULL, suite INT NOT NULL, INDEX IDX_43B9FE3C1E27F6BF (question_id), INDEX IDX_43B9FE3C8C9E392E (jeu_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('ALTER TABLE duo ADD CONSTRAINT FK_3FDA4B94C0990423 FOREIGN KEY (player1_id) REFERENCES player (id)');
        $this->addSql('ALTER TABLE duo ADD CONSTRAINT FK_3FDA4B94D22CABCD FOREIGN KEY (player2_id) REFERENCES player (id)');
        $this->addSql('ALTER TABLE jeu_player ADD CONSTRAINT FK_2B4DE5488C9E392E FOREIGN KEY (jeu_id) REFERENCES jeu (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE jeu_player ADD CONSTRAINT FK_2B4DE54899E6F5DF FOREIGN KEY (player_id) REFERENCES player (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE step ADD CONSTRAINT FK_43B9FE3C1E27F6BF FOREIGN KEY (question_id) REFERENCES question (id)');
        $this->addSql('ALTER TABLE step ADD CONSTRAINT FK_43B9FE3C8C9E392E FOREIGN KEY (jeu_id) REFERENCES jeu (id)');
    }
}
