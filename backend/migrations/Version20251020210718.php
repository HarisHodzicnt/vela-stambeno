<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20251020210718 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE properties (id UUID NOT NULL, owner_id UUID NOT NULL, property_type VARCHAR(50) NOT NULL, listing_type VARCHAR(20) NOT NULL, title VARCHAR(200) NOT NULL, description TEXT NOT NULL, address_line1 VARCHAR(255) NOT NULL, address_line2 VARCHAR(255) DEFAULT NULL, city VARCHAR(100) NOT NULL, municipality VARCHAR(100) DEFAULT NULL, postal_code VARCHAR(20) DEFAULT NULL, country VARCHAR(2) DEFAULT \'BA\' NOT NULL, latitude NUMERIC(10, 8) DEFAULT NULL, longitude NUMERIC(11, 8) DEFAULT NULL, bedrooms INT NOT NULL, bathrooms NUMERIC(3, 1) NOT NULL, size_sqm NUMERIC(10, 2) NOT NULL, floor INT DEFAULT NULL, total_floors INT DEFAULT NULL, year_built INT DEFAULT NULL, price_per_night NUMERIC(10, 2) DEFAULT NULL, price_per_month NUMERIC(10, 2) DEFAULT NULL, min_rental_nights INT DEFAULT 1 NOT NULL, max_rental_nights INT DEFAULT NULL, cleaning_fee NUMERIC(10, 2) DEFAULT \'0.00\' NOT NULL, deposit_amount NUMERIC(10, 2) DEFAULT \'0.00\' NOT NULL, sale_price NUMERIC(12, 2) DEFAULT NULL, price_negotiable BOOLEAN DEFAULT true NOT NULL, available_from DATE DEFAULT NULL, available_until DATE DEFAULT NULL, instant_book BOOLEAN DEFAULT false NOT NULL, status VARCHAR(20) DEFAULT \'draft\' NOT NULL, is_featured BOOLEAN DEFAULT false NOT NULL, cover_image_url VARCHAR(255) DEFAULT NULL, virtual_tour_url VARCHAR(255) DEFAULT NULL, average_rating NUMERIC(3, 2) DEFAULT \'0.00\' NOT NULL, total_reviews INT DEFAULT 0 NOT NULL, total_rental_bookings INT DEFAULT 0 NOT NULL, slug VARCHAR(255) NOT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, updated_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, published_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, sold_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_87C331C7989D9B62 ON properties (slug)');
        $this->addSql('CREATE INDEX idx_property_search ON properties (status, listing_type, city)');
        $this->addSql('CREATE INDEX idx_property_owner ON properties (owner_id)');
        $this->addSql('CREATE TABLE property_images (id UUID NOT NULL, property_id UUID NOT NULL, url VARCHAR(255) NOT NULL, thumbnail_url VARCHAR(255) DEFAULT NULL, caption VARCHAR(255) DEFAULT NULL, display_order INT DEFAULT 0 NOT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_9E68D116549213EC ON property_images (property_id)');
        $this->addSql('CREATE TABLE purchase_offers (id UUID NOT NULL, property_id UUID NOT NULL, buyer_id UUID NOT NULL, offer_amount NUMERIC(12, 2) NOT NULL, message TEXT DEFAULT NULL, financing_type VARCHAR(20) NOT NULL, contingencies TEXT DEFAULT NULL, proposed_closing_date DATE DEFAULT NULL, offer_valid_until TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, status VARCHAR(30) DEFAULT \'pending\' NOT NULL, counter_offer_amount NUMERIC(12, 2) DEFAULT NULL, counter_offer_message TEXT DEFAULT NULL, counter_offer_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, responded_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, response_message TEXT DEFAULT NULL, contract_signed BOOLEAN DEFAULT false NOT NULL, contract_signed_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, updated_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_1290672549213EC ON purchase_offers (property_id)');
        $this->addSql('CREATE INDEX IDX_12906726C755722 ON purchase_offers (buyer_id)');
        $this->addSql('CREATE TABLE rental_bookings (id UUID NOT NULL, property_id UUID NOT NULL, guest_id UUID NOT NULL, check_in_date DATE NOT NULL, check_out_date DATE NOT NULL, total_nights INT NOT NULL, number_of_guests INT NOT NULL, guest_phone VARCHAR(50) NOT NULL, special_requests TEXT DEFAULT NULL, base_price NUMERIC(10, 2) NOT NULL, cleaning_fee NUMERIC(10, 2) DEFAULT \'0.00\' NOT NULL, service_fee NUMERIC(10, 2) NOT NULL, total_price NUMERIC(10, 2) NOT NULL, deposit_amount NUMERIC(10, 2) DEFAULT \'0.00\' NOT NULL, payment_status VARCHAR(30) DEFAULT \'pending\' NOT NULL, payment_intent_id VARCHAR(255) DEFAULT NULL, paid_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, deposit_held BOOLEAN DEFAULT false NOT NULL, deposit_released_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, status VARCHAR(30) DEFAULT \'pending\' NOT NULL, requires_owner_approval BOOLEAN DEFAULT true NOT NULL, confirmed_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, cancelled_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, cancellation_reason TEXT DEFAULT NULL, cancelled_by UUID DEFAULT NULL, checked_in_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, checked_out_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, updated_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_4AE32506549213EC ON rental_bookings (property_id)');
        $this->addSql('CREATE INDEX IDX_4AE325069A4AA658 ON rental_bookings (guest_id)');
        $this->addSql('COMMENT ON COLUMN rental_bookings.cancelled_by IS \'(DC2Type:uuid)\'');
        $this->addSql('CREATE TABLE "users" (id UUID NOT NULL, email VARCHAR(180) NOT NULL, roles JSON NOT NULL, password VARCHAR(255) NOT NULL, first_name VARCHAR(100) NOT NULL, last_name VARCHAR(100) NOT NULL, phone VARCHAR(50) NOT NULL, avatar_url VARCHAR(255) DEFAULT NULL, date_of_birth DATE NOT NULL, is_verified BOOLEAN DEFAULT false NOT NULL, id_document_url VARCHAR(255) DEFAULT NULL, verification_status VARCHAR(20) DEFAULT \'pending\' NOT NULL, is_owner BOOLEAN DEFAULT false NOT NULL, is_business BOOLEAN DEFAULT false NOT NULL, address_line1 VARCHAR(255) DEFAULT NULL, address_line2 VARCHAR(255) DEFAULT NULL, city VARCHAR(100) DEFAULT NULL, postal_code VARCHAR(20) DEFAULT NULL, country VARCHAR(2) DEFAULT \'BA\' NOT NULL, stripe_customer_id VARCHAR(255) DEFAULT NULL, stripe_account_id VARCHAR(255) DEFAULT NULL, average_rating_as_owner NUMERIC(3, 2) DEFAULT \'0.00\' NOT NULL, average_rating_as_guest NUMERIC(3, 2) DEFAULT \'0.00\' NOT NULL, total_reviews_received INT DEFAULT 0 NOT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, updated_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, last_login_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, account_status VARCHAR(20) DEFAULT \'active\' NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_1483A5E9E7927C74 ON "users" (email)');
        $this->addSql('CREATE TABLE saved_properties (user_id UUID NOT NULL, property_id UUID NOT NULL, PRIMARY KEY(user_id, property_id))');
        $this->addSql('CREATE INDEX IDX_44FA642FA76ED395 ON saved_properties (user_id)');
        $this->addSql('CREATE INDEX IDX_44FA642F549213EC ON saved_properties (property_id)');
        $this->addSql('ALTER TABLE properties ADD CONSTRAINT FK_87C331C77E3C61F9 FOREIGN KEY (owner_id) REFERENCES "users" (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE property_images ADD CONSTRAINT FK_9E68D116549213EC FOREIGN KEY (property_id) REFERENCES properties (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE purchase_offers ADD CONSTRAINT FK_1290672549213EC FOREIGN KEY (property_id) REFERENCES properties (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE purchase_offers ADD CONSTRAINT FK_12906726C755722 FOREIGN KEY (buyer_id) REFERENCES "users" (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE rental_bookings ADD CONSTRAINT FK_4AE32506549213EC FOREIGN KEY (property_id) REFERENCES properties (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE rental_bookings ADD CONSTRAINT FK_4AE325069A4AA658 FOREIGN KEY (guest_id) REFERENCES "users" (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE saved_properties ADD CONSTRAINT FK_44FA642FA76ED395 FOREIGN KEY (user_id) REFERENCES "users" (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE saved_properties ADD CONSTRAINT FK_44FA642F549213EC FOREIGN KEY (property_id) REFERENCES properties (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('ALTER TABLE properties DROP CONSTRAINT FK_87C331C77E3C61F9');
        $this->addSql('ALTER TABLE property_images DROP CONSTRAINT FK_9E68D116549213EC');
        $this->addSql('ALTER TABLE purchase_offers DROP CONSTRAINT FK_1290672549213EC');
        $this->addSql('ALTER TABLE purchase_offers DROP CONSTRAINT FK_12906726C755722');
        $this->addSql('ALTER TABLE rental_bookings DROP CONSTRAINT FK_4AE32506549213EC');
        $this->addSql('ALTER TABLE rental_bookings DROP CONSTRAINT FK_4AE325069A4AA658');
        $this->addSql('ALTER TABLE saved_properties DROP CONSTRAINT FK_44FA642FA76ED395');
        $this->addSql('ALTER TABLE saved_properties DROP CONSTRAINT FK_44FA642F549213EC');
        $this->addSql('DROP TABLE properties');
        $this->addSql('DROP TABLE property_images');
        $this->addSql('DROP TABLE purchase_offers');
        $this->addSql('DROP TABLE rental_bookings');
        $this->addSql('DROP TABLE "users"');
        $this->addSql('DROP TABLE saved_properties');
    }
}
