<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230216173151 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SEQUENCE conversation_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE message_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE participant_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE reset_password_request_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE user_data_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE TABLE breathing_exercise (id VARCHAR(36) NOT NULL, user_id INT NOT NULL, session_number INT NOT NULL, date_create TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, duration VARCHAR(255) NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_C813B9A1A76ED395 ON breathing_exercise (user_id)');
        $this->addSql('CREATE TABLE conversation (id INT NOT NULL, last_message_id INT DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_8A8E26E9BA0E79C3 ON conversation (last_message_id)');
        $this->addSql('CREATE INDEX last_message_id_index ON conversation (last_message_id)');
        $this->addSql('CREATE TABLE lap (id VARCHAR(36) NOT NULL, breathing_exercise_id VARCHAR(36) NOT NULL, number INT NOT NULL, date_create TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, breaths INT NOT NULL, exhale_hold INT NOT NULL, inhale_hold INT NOT NULL, time VARCHAR(255) NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_926FC08CF42A680 ON lap (breathing_exercise_id)');
        $this->addSql('CREATE TABLE message (id INT NOT NULL, user_id INT DEFAULT NULL, conversation_id INT DEFAULT NULL, content TEXT NOT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_B6BD307FA76ED395 ON message (user_id)');
        $this->addSql('CREATE INDEX IDX_B6BD307F9AC0396 ON message (conversation_id)');
        $this->addSql('CREATE INDEX created_at_index ON message (created_at)');
        $this->addSql('CREATE TABLE participant (id INT NOT NULL, user_id INT DEFAULT NULL, conversation_id INT DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_D79F6B11A76ED395 ON participant (user_id)');
        $this->addSql('CREATE INDEX IDX_D79F6B119AC0396 ON participant (conversation_id)');
        $this->addSql('CREATE TABLE reset_password_request (id INT NOT NULL, user_id INT NOT NULL, selector VARCHAR(20) NOT NULL, hashed_token VARCHAR(100) NOT NULL, requested_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, expires_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_7CE748AA76ED395 ON reset_password_request (user_id)');
        $this->addSql('COMMENT ON COLUMN reset_password_request.requested_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('COMMENT ON COLUMN reset_password_request.expires_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('CREATE TABLE user_data (id INT NOT NULL, email VARCHAR(180) NOT NULL, roles JSON NOT NULL, password VARCHAR(255) NOT NULL, name VARCHAR(255) NOT NULL, confirmation_code VARCHAR(255) DEFAULT NULL, enabled BOOLEAN NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_D772BFAAE7927C74 ON user_data (email)');
        $this->addSql('ALTER TABLE breathing_exercise ADD CONSTRAINT FK_C813B9A1A76ED395 FOREIGN KEY (user_id) REFERENCES user_data (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE conversation ADD CONSTRAINT FK_8A8E26E9BA0E79C3 FOREIGN KEY (last_message_id) REFERENCES message (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE lap ADD CONSTRAINT FK_926FC08CF42A680 FOREIGN KEY (breathing_exercise_id) REFERENCES breathing_exercise (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE message ADD CONSTRAINT FK_B6BD307FA76ED395 FOREIGN KEY (user_id) REFERENCES user_data (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE message ADD CONSTRAINT FK_B6BD307F9AC0396 FOREIGN KEY (conversation_id) REFERENCES conversation (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE participant ADD CONSTRAINT FK_D79F6B11A76ED395 FOREIGN KEY (user_id) REFERENCES user_data (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE participant ADD CONSTRAINT FK_D79F6B119AC0396 FOREIGN KEY (conversation_id) REFERENCES conversation (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE reset_password_request ADD CONSTRAINT FK_7CE748AA76ED395 FOREIGN KEY (user_id) REFERENCES user_data (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('DROP SEQUENCE conversation_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE message_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE participant_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE reset_password_request_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE user_data_id_seq CASCADE');
        $this->addSql('ALTER TABLE breathing_exercise DROP CONSTRAINT FK_C813B9A1A76ED395');
        $this->addSql('ALTER TABLE conversation DROP CONSTRAINT FK_8A8E26E9BA0E79C3');
        $this->addSql('ALTER TABLE lap DROP CONSTRAINT FK_926FC08CF42A680');
        $this->addSql('ALTER TABLE message DROP CONSTRAINT FK_B6BD307FA76ED395');
        $this->addSql('ALTER TABLE message DROP CONSTRAINT FK_B6BD307F9AC0396');
        $this->addSql('ALTER TABLE participant DROP CONSTRAINT FK_D79F6B11A76ED395');
        $this->addSql('ALTER TABLE participant DROP CONSTRAINT FK_D79F6B119AC0396');
        $this->addSql('ALTER TABLE reset_password_request DROP CONSTRAINT FK_7CE748AA76ED395');
        $this->addSql('DROP TABLE breathing_exercise');
        $this->addSql('DROP TABLE conversation');
        $this->addSql('DROP TABLE lap');
        $this->addSql('DROP TABLE message');
        $this->addSql('DROP TABLE participant');
        $this->addSql('DROP TABLE reset_password_request');
        $this->addSql('DROP TABLE user_data');
    }
}
