<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20191208162121 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE drivers_attachments DROP FOREIGN KEY FK_15DEBAD9C3423909');
        $this->addSql('ALTER TABLE records DROP FOREIGN KEY FK_9C9D5846C3423909');
        $this->addSql('ALTER TABLE records DROP FOREIGN KEY FK_9C9D5846545317D1');
        $this->addSql('ALTER TABLE vehicles_attachments DROP FOREIGN KEY FK_89555552545317D1');
        $this->addSql('DROP TABLE costs');
        $this->addSql('DROP TABLE drivers');
        $this->addSql('DROP TABLE drivers_attachments');
        $this->addSql('DROP TABLE records');
        $this->addSql('DROP TABLE route');
        $this->addSql('DROP TABLE user_notify');
        $this->addSql('DROP TABLE vehicles');
        $this->addSql('DROP TABLE vehicles_attachments');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE costs (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL COLLATE utf8_unicode_ci, description VARCHAR(255) DEFAULT NULL COLLATE utf8_unicode_ci, document VARCHAR(255) DEFAULT NULL COLLATE utf8_unicode_ci, value DOUBLE PRECISION NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('CREATE TABLE drivers (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(124) NOT NULL COLLATE utf8_unicode_ci, firstname VARCHAR(124) NOT NULL COLLATE utf8_unicode_ci, city VARCHAR(124) DEFAULT NULL COLLATE utf8_unicode_ci, code_post VARCHAR(10) DEFAULT NULL COLLATE utf8_unicode_ci, street VARCHAR(124) DEFAULT NULL COLLATE utf8_unicode_ci, phone VARCHAR(12) DEFAULT NULL COLLATE utf8_unicode_ci, email VARCHAR(124) DEFAULT NULL COLLATE utf8_unicode_ci, description VARCHAR(200) DEFAULT NULL COLLATE utf8_unicode_ci, updated_at DATETIME NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('CREATE TABLE drivers_attachments (id INT AUTO_INCREMENT NOT NULL, driver_id INT DEFAULT NULL, image VARCHAR(200) NOT NULL COLLATE utf8_unicode_ci, created_at DATETIME NOT NULL, updated_at DATETIME NOT NULL, INDEX IDX_15DEBAD9C3423909 (driver_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('CREATE TABLE records (id INT AUTO_INCREMENT NOT NULL, vehicle_id INT NOT NULL, driver_id INT NOT NULL, month INT DEFAULT NULL, year INT DEFAULT NULL, INDEX IDX_9C9D5846C3423909 (driver_id), INDEX IDX_9C9D5846545317D1 (vehicle_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('CREATE TABLE route (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL COLLATE utf8_unicode_ci, description VARCHAR(255) DEFAULT NULL COLLATE utf8_unicode_ci, km INT NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('CREATE TABLE user_notify (id INT AUTO_INCREMENT NOT NULL, user_id INT NOT NULL, date DATETIME NOT NULL, ip VARCHAR(50) NOT NULL COLLATE utf8_unicode_ci, text LONGTEXT NOT NULL COLLATE utf8_unicode_ci, INDEX IDX_4429F6F5A76ED395 (user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('CREATE TABLE vehicles (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(100) NOT NULL COLLATE utf8_unicode_ci, registration VARCHAR(25) NOT NULL COLLATE utf8_unicode_ci, vin VARCHAR(25) NOT NULL COLLATE utf8_unicode_ci, first_registration VARCHAR(25) DEFAULT NULL COLLATE utf8_unicode_ci, type VARCHAR(50) DEFAULT NULL COLLATE utf8_unicode_ci, date_overview DATE DEFAULT NULL, date_insurance DATE DEFAULT NULL, date_oil DATE DEFAULT NULL, date_warranty DATE DEFAULT NULL, date_udt DATE DEFAULT NULL, date_mechanic DATE DEFAULT NULL, date_documents DATE DEFAULT NULL, description LONGTEXT DEFAULT NULL COLLATE utf8_unicode_ci, updated_at DATETIME NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('CREATE TABLE vehicles_attachments (id INT AUTO_INCREMENT NOT NULL, vehicle_id INT DEFAULT NULL, image VARCHAR(200) NOT NULL COLLATE utf8_unicode_ci, created_at DATETIME NOT NULL, updated_at DATETIME NOT NULL, INDEX IDX_89555552545317D1 (vehicle_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('ALTER TABLE drivers_attachments ADD CONSTRAINT FK_15DEBAD9C3423909 FOREIGN KEY (driver_id) REFERENCES drivers (id)');
        $this->addSql('ALTER TABLE records ADD CONSTRAINT FK_9C9D5846545317D1 FOREIGN KEY (vehicle_id) REFERENCES vehicles (id)');
        $this->addSql('ALTER TABLE records ADD CONSTRAINT FK_9C9D5846C3423909 FOREIGN KEY (driver_id) REFERENCES drivers (id)');
        $this->addSql('ALTER TABLE user_notify ADD CONSTRAINT FK_4429F6F5A76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE vehicles_attachments ADD CONSTRAINT FK_89555552545317D1 FOREIGN KEY (vehicle_id) REFERENCES vehicles (id)');
    }
}
