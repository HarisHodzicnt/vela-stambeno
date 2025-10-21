<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20251020234147 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE properties DROP CONSTRAINT FK_87C331C77E3C61F9');
        $this->addSql('ALTER TABLE properties DROP address_line2');
        $this->addSql('ALTER TABLE properties DROP postal_code');
        $this->addSql('ALTER TABLE properties DROP latitude');
        $this->addSql('ALTER TABLE properties DROP longitude');
        $this->addSql('ALTER TABLE properties DROP floor');
        $this->addSql('ALTER TABLE properties DROP total_floors');
        $this->addSql('ALTER TABLE properties DROP year_built');
        $this->addSql('ALTER TABLE properties DROP price_per_month');
        $this->addSql('ALTER TABLE properties DROP min_rental_nights');
        $this->addSql('ALTER TABLE properties DROP max_rental_nights');
        $this->addSql('ALTER TABLE properties DROP cleaning_fee');
        $this->addSql('ALTER TABLE properties DROP deposit_amount');
        $this->addSql('ALTER TABLE properties DROP price_negotiable');
        $this->addSql('ALTER TABLE properties DROP available_from');
        $this->addSql('ALTER TABLE properties DROP available_until');
        $this->addSql('ALTER TABLE properties DROP is_featured');
        $this->addSql('ALTER TABLE properties DROP virtual_tour_url');
        $this->addSql('ALTER TABLE properties DROP average_rating');
        $this->addSql('ALTER TABLE properties DROP total_reviews');
        $this->addSql('ALTER TABLE properties DROP total_rental_bookings');
        $this->addSql('ALTER TABLE properties ADD CONSTRAINT FK_87C331C77E3C61F9 FOREIGN KEY (owner_id) REFERENCES "users" (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('ALTER TABLE properties DROP CONSTRAINT fk_87c331c77e3c61f9');
        $this->addSql('ALTER TABLE properties ADD address_line2 VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE properties ADD postal_code VARCHAR(20) DEFAULT NULL');
        $this->addSql('ALTER TABLE properties ADD latitude NUMERIC(10, 8) DEFAULT NULL');
        $this->addSql('ALTER TABLE properties ADD longitude NUMERIC(11, 8) DEFAULT NULL');
        $this->addSql('ALTER TABLE properties ADD floor INT DEFAULT NULL');
        $this->addSql('ALTER TABLE properties ADD total_floors INT DEFAULT NULL');
        $this->addSql('ALTER TABLE properties ADD year_built INT DEFAULT NULL');
        $this->addSql('ALTER TABLE properties ADD price_per_month NUMERIC(10, 2) DEFAULT NULL');
        $this->addSql('ALTER TABLE properties ADD min_rental_nights INT DEFAULT 1 NOT NULL');
        $this->addSql('ALTER TABLE properties ADD max_rental_nights INT DEFAULT NULL');
        $this->addSql('ALTER TABLE properties ADD cleaning_fee NUMERIC(10, 2) DEFAULT \'0.00\' NOT NULL');
        $this->addSql('ALTER TABLE properties ADD deposit_amount NUMERIC(10, 2) DEFAULT \'0.00\' NOT NULL');
        $this->addSql('ALTER TABLE properties ADD price_negotiable BOOLEAN DEFAULT true NOT NULL');
        $this->addSql('ALTER TABLE properties ADD available_from DATE DEFAULT NULL');
        $this->addSql('ALTER TABLE properties ADD available_until DATE DEFAULT NULL');
        $this->addSql('ALTER TABLE properties ADD is_featured BOOLEAN DEFAULT false NOT NULL');
        $this->addSql('ALTER TABLE properties ADD virtual_tour_url VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE properties ADD average_rating NUMERIC(3, 2) DEFAULT \'0.00\' NOT NULL');
        $this->addSql('ALTER TABLE properties ADD total_reviews INT DEFAULT 0 NOT NULL');
        $this->addSql('ALTER TABLE properties ADD total_rental_bookings INT DEFAULT 0 NOT NULL');
        $this->addSql('ALTER TABLE properties ADD CONSTRAINT fk_87c331c77e3c61f9 FOREIGN KEY (owner_id) REFERENCES users (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
    }
}
