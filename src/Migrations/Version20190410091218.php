<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20190410091218 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE articles (id INT AUTO_INCREMENT NOT NULL, category_id INT NOT NULL, created_by INT NOT NULL, updated_by INT NOT NULL, name VARCHAR(250) NOT NULL, slug VARCHAR(250) NOT NULL, image VARCHAR(250) DEFAULT NULL, summary LONGTEXT NOT NULL, description LONGTEXT NOT NULL, views INT DEFAULT NULL, is_published TINYINT(1) DEFAULT \'0\' NOT NULL, is_checked_out TINYINT(1) DEFAULT \'0\' NOT NULL, created_at DATETIME NOT NULL, updated_at DATETIME DEFAULT NULL, UNIQUE INDEX UNIQ_BFDD3168989D9B62 (slug), INDEX IDX_BFDD316812469DE2 (category_id), INDEX IDX_BFDD3168DE12AB56 (created_by), INDEX IDX_BFDD316816FE72E1 (updated_by), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE pireaus_transaction (id INT AUTO_INCREMENT NOT NULL, client_id INT NOT NULL, support_reference_id INT NOT NULL, merchant_reference VARCHAR(50) NOT NULL, status_flag VARCHAR(12) DEFAULT NULL, response_code VARCHAR(2) DEFAULT NULL, response_description LONGTEXT DEFAULT NULL, result_code VARCHAR(5) DEFAULT NULL, result_description LONGTEXT DEFAULT NULL, approval_code VARCHAR(6) NOT NULL, package_no INT DEFAULT NULL, auth_status VARCHAR(2) DEFAULT NULL, created_at DATETIME NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE finstates (id INT NOT NULL, name VARCHAR(50) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE category (id INT AUTO_INCREMENT NOT NULL, s1id INT NOT NULL, s1level INT DEFAULT NULL, name VARCHAR(50) NOT NULL, slug VARCHAR(50) NOT NULL, image_url LONGTEXT DEFAULT NULL, description LONGTEXT DEFAULT NULL, items_count INT DEFAULT NULL, is_visible TINYINT(1) NOT NULL, priority INT NOT NULL, alternative_categories VARCHAR(50) DEFAULT NULL, INDEX SoftOne_ID_Index (s1id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE category_children (parent_id INT NOT NULL, child_id INT NOT NULL, INDEX IDX_16ED35C1727ACA70 (parent_id), INDEX IDX_16ED35C1DD62C21B (child_id), PRIMARY KEY(parent_id, child_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE paypal_transaction (id INT AUTO_INCREMENT NOT NULL, start DATETIME NOT NULL, end DATETIME DEFAULT NULL, status SMALLINT DEFAULT 0 NOT NULL, token VARCHAR(100) NOT NULL, amount NUMERIC(6, 2) DEFAULT \'0\' NOT NULL, response LONGTEXT NOT NULL COMMENT \'(DC2Type:array)\', UNIQUE INDEX UNIQ_8CB5DC995F37A13B (token), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE anosia_search_keywords (id INT AUTO_INCREMENT NOT NULL, keyword VARCHAR(100) NOT NULL, category_id INT NOT NULL, slug VARCHAR(150) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE also_viewed_products (id INT AUTO_INCREMENT NOT NULL, product_id INT NOT NULL, also_viewed_id INT NOT NULL, views INT NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE wishlist (id INT AUTO_INCREMENT NOT NULL, username VARCHAR(150) DEFAULT NULL, session_id VARCHAR(150) NOT NULL, product_id INT NOT NULL, created_at DATETIME NOT NULL, updated_at DATETIME NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE admin_category (id INT AUTO_INCREMENT NOT NULL, tree_root INT DEFAULT NULL, parent_id INT DEFAULT NULL, created_by INT NOT NULL, updated_by INT NOT NULL, name VARCHAR(128) NOT NULL, slug VARCHAR(150) NOT NULL, description LONGTEXT DEFAULT NULL, priority INT NOT NULL, metadesc VARCHAR(255) DEFAULT NULL, metakey VARCHAR(255) DEFAULT NULL, is_published TINYINT(1) DEFAULT \'0\' NOT NULL, is_checked_out TINYINT(1) DEFAULT \'0\' NOT NULL, deleted_at DATETIME DEFAULT NULL, created_at DATETIME NOT NULL, updated_at DATETIME NOT NULL, lft INT NOT NULL, rgt INT NOT NULL, lvl INT NOT NULL, INDEX IDX_92EC1EC7A977936C (tree_root), INDEX IDX_92EC1EC7727ACA70 (parent_id), INDEX IDX_92EC1EC7DE12AB56 (created_by), INDEX IDX_92EC1EC716FE72E1 (updated_by), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE cart (id INT AUTO_INCREMENT NOT NULL, product_id INT NOT NULL, username VARCHAR(150) DEFAULT NULL, session_id VARCHAR(150) DEFAULT NULL, quantity INT NOT NULL, created_at DATETIME NOT NULL, updated_at DATETIME NOT NULL, INDEX IDX_BA388B74584665A (product_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE category_top_seller (id INT AUTO_INCREMENT NOT NULL, category_id INT NOT NULL, soft_one_id INT NOT NULL, name VARCHAR(50) NOT NULL, slug VARCHAR(50) NOT NULL, image_url LONGTEXT DEFAULT NULL, INDEX IDX_8AF9290E12469DE2 (category_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE role (id SMALLINT AUTO_INCREMENT NOT NULL, name VARCHAR(30) NOT NULL, description VARCHAR(30) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE home_page_our_corner (id INT AUTO_INCREMENT NOT NULL, created_by INT NOT NULL, updated_by INT NOT NULL, category_id INT DEFAULT NULL, name VARCHAR(150) DEFAULT NULL, image LONGTEXT NOT NULL, description LONGTEXT DEFAULT NULL, is_published TINYINT(1) NOT NULL, created_at DATETIME NOT NULL, updated_at DATETIME NOT NULL, INDEX IDX_6EF4B2CBDE12AB56 (created_by), INDEX IDX_6EF4B2CB16FE72E1 (updated_by), INDEX IDX_6EF4B2CB12469DE2 (category_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE home_page_modules (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(150) NOT NULL, slug VARCHAR(100) DEFAULT NULL, priority INT NOT NULL, is_published TINYINT(1) NOT NULL, url VARCHAR(150) DEFAULT NULL, is_editable TINYINT(1) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE product (id INT NOT NULL, slug VARCHAR(200) NOT NULL, pr_code VARCHAR(50) NOT NULL, product_name LONGTEXT NOT NULL, views INT DEFAULT NULL, barcode VARCHAR(50) DEFAULT NULL, menu_id INT DEFAULT NULL, image LONGTEXT DEFAULT NULL, retail_price NUMERIC(7, 2) NOT NULL, web_price NUMERIC(7, 2) NOT NULL, discount INT DEFAULT NULL, latest_offer DATETIME DEFAULT NULL, web_visible TINYINT(1) DEFAULT NULL, active TINYINT(1) DEFAULT NULL, FULLTEXT INDEX nameIdx (product_name), FULLTEXT INDEX codeIdx (pr_code), FULLTEXT INDEX barcodeIdx (barcode), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE migration_products (id INT AUTO_INCREMENT NOT NULL, old_id INT NOT NULL, s1id INT DEFAULT NULL, name LONGTEXT NOT NULL, sku VARCHAR(100) NOT NULL, small_description LONGTEXT DEFAULT NULL, large_description LONGTEXT DEFAULT NULL, ingredients LONGTEXT DEFAULT NULL, instructions LONGTEXT DEFAULT NULL, old_slug VARCHAR(250) DEFAULT NULL, barcode VARCHAR(50) DEFAULT NULL, slug VARCHAR(250) NOT NULL, availability VARCHAR(1) DEFAULT NULL, retail_price NUMERIC(7, 2) DEFAULT NULL, web_price NUMERIC(7, 2) DEFAULT NULL, discount INT DEFAULT NULL, seo_title LONGTEXT DEFAULT NULL, seo_keywords LONGTEXT DEFAULT NULL, manufacturer VARCHAR(100) DEFAULT NULL, manufacturer_id INT DEFAULT NULL, images LONGTEXT DEFAULT NULL, image_update_error VARCHAR(1) DEFAULT NULL, web_visible VARCHAR(1) DEFAULT NULL, category_ids VARCHAR(50) DEFAULT NULL, updated VARCHAR(1) DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE users (id INT AUTO_INCREMENT NOT NULL, role_id SMALLINT NOT NULL, username VARCHAR(30) NOT NULL, password VARCHAR(255) NOT NULL, email VARCHAR(80) NOT NULL, is_active TINYINT(1) NOT NULL, last_login DATETIME DEFAULT NULL, confirmation_token VARCHAR(128) DEFAULT NULL, password_requested_at DATETIME DEFAULT NULL, password_request_counter SMALLINT DEFAULT NULL, UNIQUE INDEX UNIQ_1483A5E9F85E0677 (username), UNIQUE INDEX UNIQ_1483A5E9E7927C74 (email), INDEX IDX_1483A5E9D60322AC (role_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE country (id INT AUTO_INCREMENT NOT NULL, name TINYTEXT NOT NULL, int_code TINYTEXT NOT NULL, s1id INT NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE slider (id INT AUTO_INCREMENT NOT NULL, created_by INT NOT NULL, updated_by INT NOT NULL, category_id INT DEFAULT NULL, admin_category_id INT DEFAULT NULL, name VARCHAR(150) DEFAULT NULL, image LONGTEXT NOT NULL, url LONGTEXT DEFAULT NULL, priority INT NOT NULL, description LONGTEXT DEFAULT NULL, is_published TINYINT(1) NOT NULL, created_at DATETIME NOT NULL, updated_at DATETIME NOT NULL, INDEX IDX_CFC71007DE12AB56 (created_by), INDEX IDX_CFC7100716FE72E1 (updated_by), INDEX IDX_CFC7100712469DE2 (category_id), INDEX IDX_CFC710078F5CD4EB (admin_category_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE availability_types (id INT AUTO_INCREMENT NOT NULL, s1id INT NOT NULL, name VARCHAR(100) NOT NULL, from_days VARCHAR(50) NOT NULL, to_days VARCHAR(50) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE manufacturer (id INT NOT NULL, code INT NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE parameters (id INT AUTO_INCREMENT NOT NULL, name TINYTEXT NOT NULL, s1value TINYTEXT NOT NULL, description LONGTEXT NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE articles ADD CONSTRAINT FK_BFDD316812469DE2 FOREIGN KEY (category_id) REFERENCES admin_category (id)');
        $this->addSql('ALTER TABLE articles ADD CONSTRAINT FK_BFDD3168DE12AB56 FOREIGN KEY (created_by) REFERENCES users (id)');
        $this->addSql('ALTER TABLE articles ADD CONSTRAINT FK_BFDD316816FE72E1 FOREIGN KEY (updated_by) REFERENCES users (id)');
        $this->addSql('ALTER TABLE category_children ADD CONSTRAINT FK_16ED35C1727ACA70 FOREIGN KEY (parent_id) REFERENCES category (id)');
        $this->addSql('ALTER TABLE category_children ADD CONSTRAINT FK_16ED35C1DD62C21B FOREIGN KEY (child_id) REFERENCES category (id)');
        $this->addSql('ALTER TABLE admin_category ADD CONSTRAINT FK_92EC1EC7A977936C FOREIGN KEY (tree_root) REFERENCES admin_category (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE admin_category ADD CONSTRAINT FK_92EC1EC7727ACA70 FOREIGN KEY (parent_id) REFERENCES admin_category (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE admin_category ADD CONSTRAINT FK_92EC1EC7DE12AB56 FOREIGN KEY (created_by) REFERENCES users (id)');
        $this->addSql('ALTER TABLE admin_category ADD CONSTRAINT FK_92EC1EC716FE72E1 FOREIGN KEY (updated_by) REFERENCES users (id)');
        $this->addSql('ALTER TABLE cart ADD CONSTRAINT FK_BA388B74584665A FOREIGN KEY (product_id) REFERENCES product (id)');
        $this->addSql('ALTER TABLE category_top_seller ADD CONSTRAINT FK_8AF9290E12469DE2 FOREIGN KEY (category_id) REFERENCES category (id)');
        $this->addSql('ALTER TABLE home_page_our_corner ADD CONSTRAINT FK_6EF4B2CBDE12AB56 FOREIGN KEY (created_by) REFERENCES users (id)');
        $this->addSql('ALTER TABLE home_page_our_corner ADD CONSTRAINT FK_6EF4B2CB16FE72E1 FOREIGN KEY (updated_by) REFERENCES users (id)');
        $this->addSql('ALTER TABLE home_page_our_corner ADD CONSTRAINT FK_6EF4B2CB12469DE2 FOREIGN KEY (category_id) REFERENCES category (id)');
        $this->addSql('ALTER TABLE users ADD CONSTRAINT FK_1483A5E9D60322AC FOREIGN KEY (role_id) REFERENCES role (id)');
        $this->addSql('ALTER TABLE slider ADD CONSTRAINT FK_CFC71007DE12AB56 FOREIGN KEY (created_by) REFERENCES users (id)');
        $this->addSql('ALTER TABLE slider ADD CONSTRAINT FK_CFC7100716FE72E1 FOREIGN KEY (updated_by) REFERENCES users (id)');
        $this->addSql('ALTER TABLE slider ADD CONSTRAINT FK_CFC7100712469DE2 FOREIGN KEY (category_id) REFERENCES category (id)');
        $this->addSql('ALTER TABLE slider ADD CONSTRAINT FK_CFC710078F5CD4EB FOREIGN KEY (admin_category_id) REFERENCES admin_category (id)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE category_children DROP FOREIGN KEY FK_16ED35C1727ACA70');
        $this->addSql('ALTER TABLE category_children DROP FOREIGN KEY FK_16ED35C1DD62C21B');
        $this->addSql('ALTER TABLE category_top_seller DROP FOREIGN KEY FK_8AF9290E12469DE2');
        $this->addSql('ALTER TABLE home_page_our_corner DROP FOREIGN KEY FK_6EF4B2CB12469DE2');
        $this->addSql('ALTER TABLE slider DROP FOREIGN KEY FK_CFC7100712469DE2');
        $this->addSql('ALTER TABLE articles DROP FOREIGN KEY FK_BFDD316812469DE2');
        $this->addSql('ALTER TABLE admin_category DROP FOREIGN KEY FK_92EC1EC7A977936C');
        $this->addSql('ALTER TABLE admin_category DROP FOREIGN KEY FK_92EC1EC7727ACA70');
        $this->addSql('ALTER TABLE slider DROP FOREIGN KEY FK_CFC710078F5CD4EB');
        $this->addSql('ALTER TABLE users DROP FOREIGN KEY FK_1483A5E9D60322AC');
        $this->addSql('ALTER TABLE cart DROP FOREIGN KEY FK_BA388B74584665A');
        $this->addSql('ALTER TABLE articles DROP FOREIGN KEY FK_BFDD3168DE12AB56');
        $this->addSql('ALTER TABLE articles DROP FOREIGN KEY FK_BFDD316816FE72E1');
        $this->addSql('ALTER TABLE admin_category DROP FOREIGN KEY FK_92EC1EC7DE12AB56');
        $this->addSql('ALTER TABLE admin_category DROP FOREIGN KEY FK_92EC1EC716FE72E1');
        $this->addSql('ALTER TABLE home_page_our_corner DROP FOREIGN KEY FK_6EF4B2CBDE12AB56');
        $this->addSql('ALTER TABLE home_page_our_corner DROP FOREIGN KEY FK_6EF4B2CB16FE72E1');
        $this->addSql('ALTER TABLE slider DROP FOREIGN KEY FK_CFC71007DE12AB56');
        $this->addSql('ALTER TABLE slider DROP FOREIGN KEY FK_CFC7100716FE72E1');
        $this->addSql('DROP TABLE articles');
        $this->addSql('DROP TABLE pireaus_transaction');
        $this->addSql('DROP TABLE finstates');
        $this->addSql('DROP TABLE category');
        $this->addSql('DROP TABLE category_children');
        $this->addSql('DROP TABLE paypal_transaction');
        $this->addSql('DROP TABLE anosia_search_keywords');
        $this->addSql('DROP TABLE also_viewed_products');
        $this->addSql('DROP TABLE wishlist');
        $this->addSql('DROP TABLE admin_category');
        $this->addSql('DROP TABLE cart');
        $this->addSql('DROP TABLE category_top_seller');
        $this->addSql('DROP TABLE role');
        $this->addSql('DROP TABLE home_page_our_corner');
        $this->addSql('DROP TABLE home_page_modules');
        $this->addSql('DROP TABLE product');
        $this->addSql('DROP TABLE migration_products');
        $this->addSql('DROP TABLE users');
        $this->addSql('DROP TABLE country');
        $this->addSql('DROP TABLE slider');
        $this->addSql('DROP TABLE availability_types');
        $this->addSql('DROP TABLE manufacturer');
        $this->addSql('DROP TABLE parameters');
    }
}
