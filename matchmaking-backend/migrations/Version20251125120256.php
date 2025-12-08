<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20251125120256 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE messenger_messages (id BIGINT AUTO_INCREMENT NOT NULL, body LONGTEXT NOT NULL, headers LONGTEXT NOT NULL, queue_name VARCHAR(190) NOT NULL, created_at DATETIME NOT NULL, available_at DATETIME NOT NULL, delivered_at DATETIME DEFAULT NULL, INDEX IDX_75EA56E0FB7336F0 (queue_name), INDEX IDX_75EA56E0E3BD61CE (available_at), INDEX IDX_75EA56E016BA31DB (delivered_at), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('ALTER TABLE user_quest DROP FOREIGN KEY `FK_USERQUEST_QUEST`');
        $this->addSql('ALTER TABLE user_quest DROP FOREIGN KEY `FK_USERQUEST_USER`');
        $this->addSql('DROP TABLE quest');
        $this->addSql('DROP TABLE user_quest');
        $this->addSql('ALTER TABLE user DROP gender, DROP age, DROP location, DROP avatar, DROP badges, DROP created_at, CHANGE username username VARCHAR(50) DEFAULT NULL, CHANGE bio bio VARCHAR(255) DEFAULT NULL, CHANGE level level INT DEFAULT 1 NOT NULL, CHANGE xp xp INT DEFAULT 0 NOT NULL');
        $this->addSql('ALTER TABLE user RENAME INDEX uniq_identifier_email TO UNIQ_8D93D649E7927C74');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE quest (id INT AUTO_INCREMENT NOT NULL, title VARCHAR(255) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_0900_ai_ci`, description TEXT CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_0900_ai_ci`, xp_reward INT NOT NULL, is_daily TINYINT(1) DEFAULT 0, PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_0900_ai_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('CREATE TABLE user_quest (user_id INT NOT NULL, quest_id INT NOT NULL, completed_at DATETIME DEFAULT CURRENT_TIMESTAMP, INDEX IDX_USER_QUEST_QUEST (quest_id), INDEX IDX_USER_QUEST_USER (user_id), PRIMARY KEY (user_id, quest_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_0900_ai_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('ALTER TABLE user_quest ADD CONSTRAINT `FK_USERQUEST_QUEST` FOREIGN KEY (quest_id) REFERENCES quest (id) ON UPDATE NO ACTION ON DELETE CASCADE');
        $this->addSql('ALTER TABLE user_quest ADD CONSTRAINT `FK_USERQUEST_USER` FOREIGN KEY (user_id) REFERENCES user (id) ON UPDATE NO ACTION ON DELETE CASCADE');
        $this->addSql('DROP TABLE messenger_messages');
        $this->addSql('ALTER TABLE `user` ADD gender VARCHAR(20) DEFAULT NULL, ADD age INT DEFAULT NULL, ADD location VARCHAR(255) DEFAULT NULL, ADD avatar VARCHAR(255) DEFAULT NULL, ADD badges JSON DEFAULT \'json_array()\', ADD created_at DATETIME DEFAULT CURRENT_TIMESTAMP, CHANGE username username VARCHAR(100) DEFAULT NULL, CHANGE bio bio TEXT DEFAULT NULL, CHANGE level level INT DEFAULT 1, CHANGE xp xp INT DEFAULT 0');
        $this->addSql('ALTER TABLE `user` RENAME INDEX uniq_8d93d649e7927c74 TO UNIQ_IDENTIFIER_EMAIL');
    }
}
