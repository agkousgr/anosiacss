<?php declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20181212152705 extends AbstractMigration
{
    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE category ADD alternative_categories VARCHAR(50) DEFAULT NULL');
        $this->addSql('ALTER TABLE home_page_modules ADD is_editable TINYINT(1) NOT NULL, CHANGE id id INT AUTO_INCREMENT NOT NULL');
        $this->addSql('ALTER TABLE migration_products ADD old_id INT NOT NULL, ADD slug VARCHAR(100) DEFAULT NULL, ADD web_visible VARCHAR(1) DEFAULT NULL, CHANGE id id INT AUTO_INCREMENT NOT NULL, CHANGE s1id s1id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE also_viewed_products CHANGE id id INT AUTO_INCREMENT NOT NULL');
        $this->addSql('ALTER TABLE articles CHANGE id id INT AUTO_INCREMENT NOT NULL, ADD PRIMARY KEY (id)');
        $this->addSql('ALTER TABLE articles ADD CONSTRAINT FK_BFDD316812469DE2 FOREIGN KEY (category_id) REFERENCES admin_category (id)');
        $this->addSql('ALTER TABLE articles ADD CONSTRAINT FK_BFDD3168DE12AB56 FOREIGN KEY (created_by) REFERENCES users (id)');
        $this->addSql('ALTER TABLE articles ADD CONSTRAINT FK_BFDD316816FE72E1 FOREIGN KEY (updated_by) REFERENCES users (id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_BFDD3168989D9B62 ON articles (slug)');
        $this->addSql('CREATE INDEX IDX_BFDD316812469DE2 ON articles (category_id)');
        $this->addSql('CREATE INDEX IDX_BFDD3168DE12AB56 ON articles (created_by)');
        $this->addSql('CREATE INDEX IDX_BFDD316816FE72E1 ON articles (updated_by)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE also_viewed_products CHANGE id id INT NOT NULL');
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
        $this->addSql('ALTER TABLE category DROP alternative_categories');
        $this->addSql('ALTER TABLE home_page_modules DROP is_editable, DROP slug, CHANGE id id INT NOT NULL');
        $this->addSql('ALTER TABLE migration_products DROP old_id, DROP web_visible, CHANGE id id INT NOT NULL, CHANGE s1id s1id INT NOT NULL');
    }
}
