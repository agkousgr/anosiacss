<?php declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20181126164324 extends AbstractMigration
{
    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE admin_category ADD CONSTRAINT FK_92EC1EC7DE12AB56 FOREIGN KEY (created_by) REFERENCES users (id)');
        $this->addSql('ALTER TABLE admin_category ADD CONSTRAINT FK_92EC1EC716FE72E1 FOREIGN KEY (updated_by) REFERENCES users (id)');
        $this->addSql('ALTER TABLE users ADD CONSTRAINT FK_1483A5E9D60322AC FOREIGN KEY (role_id) REFERENCES role (id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_1483A5E9F85E0677 ON users (username)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_1483A5E9E7927C74 ON users (email)');
        $this->addSql('CREATE INDEX IDX_1483A5E9D60322AC ON users (role_id)');
        $this->addSql('ALTER TABLE orders_web_id CHANGE id id INT AUTO_INCREMENT NOT NULL, ADD PRIMARY KEY (id)');
        $this->addSql('ALTER TABLE slider ADD CONSTRAINT FK_CFC71007DE12AB56 FOREIGN KEY (created_by) REFERENCES users (id)');
        $this->addSql('ALTER TABLE slider ADD CONSTRAINT FK_CFC7100716FE72E1 FOREIGN KEY (updated_by) REFERENCES users (id)');
        $this->addSql('ALTER TABLE slider ADD CONSTRAINT FK_CFC7100712469DE2 FOREIGN KEY (category_id) REFERENCES category (id)');
        $this->addSql('CREATE INDEX IDX_CFC71007DE12AB56 ON slider (created_by)');
        $this->addSql('CREATE INDEX IDX_CFC7100716FE72E1 ON slider (updated_by)');
        $this->addSql('CREATE INDEX IDX_CFC7100712469DE2 ON slider (category_id)');
        $this->addSql('ALTER TABLE home_page_modules CHANGE id id INT AUTO_INCREMENT NOT NULL');
        $this->addSql('ALTER TABLE migration_products ADD old_id INT NOT NULL, ADD web_visible VARCHAR(1) DEFAULT NULL, CHANGE id id INT AUTO_INCREMENT NOT NULL, CHANGE s1id s1id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE also_viewed_products CHANGE id id INT AUTO_INCREMENT NOT NULL');
        $this->addSql('ALTER TABLE anosia_search_keywords CHANGE id id INT AUTO_INCREMENT NOT NULL');
        $this->addSql('ALTER TABLE articles CHANGE id id INT AUTO_INCREMENT NOT NULL, ADD PRIMARY KEY (id)');
        $this->addSql('ALTER TABLE articles ADD CONSTRAINT FK_BFDD316812469DE2 FOREIGN KEY (category_id) REFERENCES admin_category (id)');
        $this->addSql('ALTER TABLE articles ADD CONSTRAINT FK_BFDD3168DE12AB56 FOREIGN KEY (created_by) REFERENCES users (id)');
        $this->addSql('ALTER TABLE articles ADD CONSTRAINT FK_BFDD316816FE72E1 FOREIGN KEY (updated_by) REFERENCES users (id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_BFDD3168989D9B62 ON articles (slug)');
        $this->addSql('CREATE INDEX IDX_BFDD316812469DE2 ON articles (category_id)');
        $this->addSql('CREATE INDEX IDX_BFDD3168DE12AB56 ON articles (created_by)');
        $this->addSql('CREATE INDEX IDX_BFDD316816FE72E1 ON articles (updated_by)');
        $this->addSql('ALTER TABLE product_views CHANGE id id INT AUTO_INCREMENT NOT NULL, ADD PRIMARY KEY (id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_B0ACE7DB4584665A ON product_views (product_id)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE admin_category DROP FOREIGN KEY FK_92EC1EC7DE12AB56');
        $this->addSql('ALTER TABLE admin_category DROP FOREIGN KEY FK_92EC1EC716FE72E1');
        $this->addSql('ALTER TABLE also_viewed_products CHANGE id id INT NOT NULL');
        $this->addSql('ALTER TABLE anosia_search_keywords CHANGE id id INT NOT NULL');
        $this->addSql('ALTER TABLE articles MODIFY id INT NOT NULL');
        $this->addSql('ALTER TABLE articles DROP FOREIGN KEY FK_BFDD316812469DE2');
        $this->addSql('ALTER TABLE articles DROP FOREIGN KEY FK_BFDD3168DE12AB56');
        $this->addSql('ALTER TABLE articles DROP FOREIGN KEY FK_BFDD316816FE72E1');
        $this->addSql('DROP INDEX UNIQ_BFDD3168989D9B62 ON articles');
        $this->addSql('DROP INDEX IDX_BFDD316812469DE2 ON articles');
        $this->addSql('DROP INDEX IDX_BFDD3168DE12AB56 ON articles');
        $this->addSql('DROP INDEX IDX_BFDD316816FE72E1 ON articles');
        $this->addSql('ALTER TABLE articles DROP PRIMARY KEY');
        $this->addSql('ALTER TABLE articles CHANGE id id INT NOT NULL');
        $this->addSql('ALTER TABLE home_page_modules CHANGE id id INT NOT NULL');
        $this->addSql('ALTER TABLE migration_products DROP old_id, DROP web_visible, CHANGE id id INT NOT NULL, CHANGE s1id s1id INT NOT NULL');
        $this->addSql('ALTER TABLE orders_web_id MODIFY id INT NOT NULL');
        $this->addSql('ALTER TABLE orders_web_id DROP PRIMARY KEY');
        $this->addSql('ALTER TABLE orders_web_id CHANGE id id INT NOT NULL');
        $this->addSql('ALTER TABLE product_views MODIFY id INT NOT NULL');
        $this->addSql('DROP INDEX UNIQ_B0ACE7DB4584665A ON product_views');
        $this->addSql('ALTER TABLE product_views DROP PRIMARY KEY');
        $this->addSql('ALTER TABLE product_views CHANGE id id INT NOT NULL');
        $this->addSql('ALTER TABLE slider DROP FOREIGN KEY FK_CFC71007DE12AB56');
        $this->addSql('ALTER TABLE slider DROP FOREIGN KEY FK_CFC7100716FE72E1');
        $this->addSql('ALTER TABLE slider DROP FOREIGN KEY FK_CFC7100712469DE2');
        $this->addSql('DROP INDEX IDX_CFC71007DE12AB56 ON slider');
        $this->addSql('DROP INDEX IDX_CFC7100716FE72E1 ON slider');
        $this->addSql('DROP INDEX IDX_CFC7100712469DE2 ON slider');
        $this->addSql('ALTER TABLE users DROP FOREIGN KEY FK_1483A5E9D60322AC');
        $this->addSql('DROP INDEX UNIQ_1483A5E9F85E0677 ON users');
        $this->addSql('DROP INDEX UNIQ_1483A5E9E7927C74 ON users');
        $this->addSql('DROP INDEX IDX_1483A5E9D60322AC ON users');
    }
}
