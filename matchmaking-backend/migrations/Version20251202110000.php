<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Migration pour créer toutes les tables de l'application matchmaking
 */
final class Version20251202110000 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Création de toutes les tables pour l\'application matchmaking (ProfileTag, MatchRequest, Match, Message, Badge, UserBadge, Quest, UserQuest, Notification, Report)';
    }

    public function up(Schema $schema): void
    {
        // Mise à jour de la table user avec les nouveaux champs
        // Note: Si certaines colonnes existent déjà, vous devrez les supprimer ou commenter les lignes correspondantes
        $this->addSql('ALTER TABLE `user` ADD gender VARCHAR(20) DEFAULT NULL');
        $this->addSql('ALTER TABLE `user` ADD age INT DEFAULT NULL');
        $this->addSql('ALTER TABLE `user` ADD location VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE `user` ADD latitude NUMERIC(10, 7) DEFAULT NULL');
        $this->addSql('ALTER TABLE `user` ADD longitude NUMERIC(10, 7) DEFAULT NULL');
        $this->addSql('ALTER TABLE `user` ADD preferences JSON DEFAULT NULL');
        $this->addSql('ALTER TABLE `user` ADD score INT DEFAULT 0 NOT NULL');
        $this->addSql('ALTER TABLE `user` ADD avatar_url VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE `user` ADD created_at DATETIME NOT NULL');
        $this->addSql('ALTER TABLE `user` ADD updated_at DATETIME DEFAULT NULL');
        
        // Modifier bio en TEXT si ce n'est pas déjà fait
        $this->addSql('ALTER TABLE `user` MODIFY COLUMN bio TEXT DEFAULT NULL');

        // Table ProfileTag
        $this->addSql('CREATE TABLE profile_tag (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(100) NOT NULL, UNIQUE INDEX UNIQ_profile_tag_name (name), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');

        // Table de liaison user_profile_tag
        $this->addSql('CREATE TABLE user_profile_tag (user_id INT NOT NULL, profile_tag_id INT NOT NULL, INDEX IDX_user_profile_tag_user (user_id), INDEX IDX_user_profile_tag_tag (profile_tag_id), PRIMARY KEY(user_id, profile_tag_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE user_profile_tag ADD CONSTRAINT FK_user_profile_tag_user FOREIGN KEY (user_id) REFERENCES `user` (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE user_profile_tag ADD CONSTRAINT FK_user_profile_tag_tag FOREIGN KEY (profile_tag_id) REFERENCES profile_tag (id) ON DELETE CASCADE');

        // Table MatchRequest
        $this->addSql('CREATE TABLE match_request (id INT AUTO_INCREMENT NOT NULL, from_user_id INT NOT NULL, to_user_id INT NOT NULL, type VARCHAR(20) NOT NULL, status VARCHAR(20) NOT NULL, created_at DATETIME NOT NULL, INDEX IDX_match_request_from_user (from_user_id), INDEX IDX_match_request_to_user (to_user_id), INDEX IDX_match_request_status (status), INDEX IDX_match_request_users (from_user_id, to_user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE match_request ADD CONSTRAINT FK_match_request_from_user FOREIGN KEY (from_user_id) REFERENCES `user` (id)');
        $this->addSql('ALTER TABLE match_request ADD CONSTRAINT FK_match_request_to_user FOREIGN KEY (to_user_id) REFERENCES `user` (id)');

        // Table user_match (Match)
        $this->addSql('CREATE TABLE user_match (id INT AUTO_INCREMENT NOT NULL, user_a_id INT NOT NULL, user_b_id INT NOT NULL, created_at DATETIME NOT NULL, last_interaction_at DATETIME DEFAULT NULL, INDEX IDX_user_match_user_a (user_a_id), INDEX IDX_user_match_user_b (user_b_id), INDEX IDX_user_match_users (user_a_id, user_b_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE user_match ADD CONSTRAINT FK_user_match_user_a FOREIGN KEY (user_a_id) REFERENCES `user` (id)');
        $this->addSql('ALTER TABLE user_match ADD CONSTRAINT FK_user_match_user_b FOREIGN KEY (user_b_id) REFERENCES `user` (id)');

        // Table Message
        $this->addSql('CREATE TABLE message (id INT AUTO_INCREMENT NOT NULL, match_id INT NOT NULL, sender_id INT NOT NULL, content LONGTEXT NOT NULL, read_at DATETIME DEFAULT NULL, created_at DATETIME NOT NULL, INDEX IDX_message_match (match_id), INDEX IDX_message_sender (sender_id), INDEX IDX_message_match_created (match_id, created_at), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE message ADD CONSTRAINT FK_message_match FOREIGN KEY (match_id) REFERENCES user_match (id)');
        $this->addSql('ALTER TABLE message ADD CONSTRAINT FK_message_sender FOREIGN KEY (sender_id) REFERENCES `user` (id)');

        // Table Badge
        $this->addSql('CREATE TABLE badge (id INT AUTO_INCREMENT NOT NULL, code VARCHAR(50) NOT NULL, name VARCHAR(255) NOT NULL, description TEXT DEFAULT NULL, icon VARCHAR(255) DEFAULT NULL, xp_reward INT DEFAULT 0 NOT NULL, UNIQUE INDEX UNIQ_badge_code (code), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');

        // Table UserBadge
        $this->addSql('CREATE TABLE user_badge (id INT AUTO_INCREMENT NOT NULL, user_id INT NOT NULL, badge_id INT NOT NULL, awarded_at DATETIME NOT NULL, INDEX IDX_user_badge_user (user_id), INDEX IDX_user_badge_badge (badge_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE user_badge ADD CONSTRAINT FK_user_badge_user FOREIGN KEY (user_id) REFERENCES `user` (id)');
        $this->addSql('ALTER TABLE user_badge ADD CONSTRAINT FK_user_badge_badge FOREIGN KEY (badge_id) REFERENCES badge (id)');

        // Table Quest
        $this->addSql('CREATE TABLE quest (id INT AUTO_INCREMENT NOT NULL, code VARCHAR(50) NOT NULL, title VARCHAR(255) NOT NULL, description TEXT DEFAULT NULL, xp_reward INT DEFAULT 0 NOT NULL, conditions JSON DEFAULT NULL, UNIQUE INDEX UNIQ_quest_code (code), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');

        // Table UserQuest
        $this->addSql('CREATE TABLE user_quest (id INT AUTO_INCREMENT NOT NULL, user_id INT NOT NULL, quest_id INT NOT NULL, progress JSON DEFAULT NULL, completed_at DATETIME DEFAULT NULL, INDEX IDX_user_quest_user (user_id), INDEX IDX_user_quest_quest (quest_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE user_quest ADD CONSTRAINT FK_user_quest_user FOREIGN KEY (user_id) REFERENCES `user` (id)');
        $this->addSql('ALTER TABLE user_quest ADD CONSTRAINT FK_user_quest_quest FOREIGN KEY (quest_id) REFERENCES quest (id)');

        // Table Notification
        $this->addSql('CREATE TABLE notification (id INT AUTO_INCREMENT NOT NULL, user_id INT NOT NULL, type VARCHAR(50) NOT NULL, payload JSON DEFAULT NULL, read_at DATETIME DEFAULT NULL, created_at DATETIME NOT NULL, INDEX IDX_notification_user (user_id), INDEX IDX_notification_user_read (user_id, read_at), INDEX IDX_notification_created (created_at), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE notification ADD CONSTRAINT FK_notification_user FOREIGN KEY (user_id) REFERENCES `user` (id)');

        // Table Report
        $this->addSql('CREATE TABLE report (id INT AUTO_INCREMENT NOT NULL, reporter_id INT NOT NULL, target_user_id INT NOT NULL, reason VARCHAR(255) NOT NULL, details TEXT DEFAULT NULL, status VARCHAR(50) NOT NULL, created_at DATETIME NOT NULL, reviewed_at DATETIME DEFAULT NULL, INDEX IDX_report_reporter (reporter_id), INDEX IDX_report_target (target_user_id), INDEX IDX_report_target_status (target_user_id, status), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE report ADD CONSTRAINT FK_report_reporter FOREIGN KEY (reporter_id) REFERENCES `user` (id)');
        $this->addSql('ALTER TABLE report ADD CONSTRAINT FK_report_target FOREIGN KEY (target_user_id) REFERENCES `user` (id)');
    }

    public function down(Schema $schema): void
    {
        // Supprimer les tables dans l'ordre inverse (en respectant les foreign keys)
        $this->addSql('DROP TABLE report');
        $this->addSql('DROP TABLE notification');
        $this->addSql('DROP TABLE user_quest');
        $this->addSql('DROP TABLE quest');
        $this->addSql('DROP TABLE user_badge');
        $this->addSql('DROP TABLE badge');
        $this->addSql('DROP TABLE message');
        $this->addSql('DROP TABLE user_match');
        $this->addSql('DROP TABLE match_request');
        $this->addSql('DROP TABLE user_profile_tag');
        $this->addSql('DROP TABLE profile_tag');

        // Retirer les nouveaux champs de la table user
        $this->addSql('ALTER TABLE `user` DROP COLUMN gender');
        $this->addSql('ALTER TABLE `user` DROP COLUMN age');
        $this->addSql('ALTER TABLE `user` DROP COLUMN location');
        $this->addSql('ALTER TABLE `user` DROP COLUMN latitude');
        $this->addSql('ALTER TABLE `user` DROP COLUMN longitude');
        $this->addSql('ALTER TABLE `user` DROP COLUMN preferences');
        $this->addSql('ALTER TABLE `user` DROP COLUMN score');
        $this->addSql('ALTER TABLE `user` DROP COLUMN avatar_url');
        $this->addSql('ALTER TABLE `user` DROP COLUMN created_at');
        $this->addSql('ALTER TABLE `user` DROP COLUMN updated_at');
        $this->addSql('ALTER TABLE `user` MODIFY COLUMN bio VARCHAR(255) DEFAULT NULL');
    }
}

