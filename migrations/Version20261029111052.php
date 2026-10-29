<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20261029111052 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE bookings (id INT AUTO_INCREMENT NOT NULL, session_title VARCHAR(255) NOT NULL, price VARCHAR(50) NOT NULL, scheduled_at DATETIME NOT NULL, status VARCHAR(30) NOT NULL, client_notes LONGTEXT DEFAULT NULL, coach_notes LONGTEXT DEFAULT NULL, created_at DATETIME NOT NULL, client_id INT NOT NULL, coach_id INT NOT NULL, session_id INT DEFAULT NULL, INDEX IDX_7A853C3519EB6921 (client_id), INDEX IDX_7A853C353C105691 (coach_id), INDEX IDX_7A853C35613FECDF (session_id), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE coach_sessions (id INT AUTO_INCREMENT NOT NULL, title VARCHAR(255) NOT NULL, duration_minutes INT NOT NULL, price VARCHAR(50) NOT NULL, coach_id INT NOT NULL, INDEX IDX_E2DC84593C105691 (coach_id), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE coaches (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(100) NOT NULL, specialization VARCHAR(100) NOT NULL, tier VARCHAR(20) NOT NULL, status VARCHAR(20) NOT NULL, rating VARCHAR(10) NOT NULL, image VARCHAR(255) DEFAULT NULL, bio LONGTEXT DEFAULT NULL, hourly_rate VARCHAR(50) DEFAULT NULL, created_at DATETIME NOT NULL, user_id INT DEFAULT NULL, UNIQUE INDEX UNIQ_C4131765A76ED395 (user_id), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE notifications (id INT AUTO_INCREMENT NOT NULL, type VARCHAR(50) NOT NULL, title VARCHAR(150) NOT NULL, message LONGTEXT NOT NULL, is_read TINYINT NOT NULL, created_at DATETIME NOT NULL, recipient_id INT NOT NULL, booking_id INT DEFAULT NULL, INDEX IDX_6000B0D3E92F8F78 (recipient_id), INDEX IDX_6000B0D33301C60 (booking_id), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE users (id INT AUTO_INCREMENT NOT NULL, email VARCHAR(180) NOT NULL, password VARCHAR(255) NOT NULL, steam_name VARCHAR(100) NOT NULL, steam_id VARCHAR(17) NOT NULL, roles JSON NOT NULL, created_at DATETIME NOT NULL, UNIQUE INDEX UNIQ_1483A5E9E7927C74 (email), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE messenger_messages (id BIGINT AUTO_INCREMENT NOT NULL, body LONGTEXT NOT NULL, headers LONGTEXT NOT NULL, queue_name VARCHAR(190) NOT NULL, created_at DATETIME NOT NULL, available_at DATETIME NOT NULL, delivered_at DATETIME DEFAULT NULL, INDEX IDX_75EA56E0FB7336F0E3BD61CE16BA31DBBF396750 (queue_name, available_at, delivered_at, id), PRIMARY KEY (id))');
        $this->addSql('ALTER TABLE bookings ADD CONSTRAINT FK_7A853C3519EB6921 FOREIGN KEY (client_id) REFERENCES users (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE bookings ADD CONSTRAINT FK_7A853C353C105691 FOREIGN KEY (coach_id) REFERENCES coaches (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE bookings ADD CONSTRAINT FK_7A853C35613FECDF FOREIGN KEY (session_id) REFERENCES coach_sessions (id) ON DELETE SET NULL');
        $this->addSql('ALTER TABLE coach_sessions ADD CONSTRAINT FK_E2DC84593C105691 FOREIGN KEY (coach_id) REFERENCES coaches (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE coaches ADD CONSTRAINT FK_C4131765A76ED395 FOREIGN KEY (user_id) REFERENCES users (id) ON DELETE SET NULL');
        $this->addSql('ALTER TABLE notifications ADD CONSTRAINT FK_6000B0D3E92F8F78 FOREIGN KEY (recipient_id) REFERENCES users (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE notifications ADD CONSTRAINT FK_6000B0D33301C60 FOREIGN KEY (booking_id) REFERENCES bookings (id) ON DELETE CASCADE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE bookings DROP FOREIGN KEY FK_7A853C3519EB6921');
        $this->addSql('ALTER TABLE bookings DROP FOREIGN KEY FK_7A853C353C105691');
        $this->addSql('ALTER TABLE bookings DROP FOREIGN KEY FK_7A853C35613FECDF');
        $this->addSql('ALTER TABLE coach_sessions DROP FOREIGN KEY FK_E2DC84593C105691');
        $this->addSql('ALTER TABLE coaches DROP FOREIGN KEY FK_C4131765A76ED395');
        $this->addSql('ALTER TABLE notifications DROP FOREIGN KEY FK_6000B0D3E92F8F78');
        $this->addSql('ALTER TABLE notifications DROP FOREIGN KEY FK_6000B0D33301C60');
        $this->addSql('DROP TABLE bookings');
        $this->addSql('DROP TABLE coach_sessions');
        $this->addSql('DROP TABLE coaches');
        $this->addSql('DROP TABLE notifications');
        $this->addSql('DROP TABLE users');
        $this->addSql('DROP TABLE messenger_messages');
    }
}
