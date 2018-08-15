<?php declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20180813212432 extends AbstractMigration
{
    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE blog_category (id INT AUTO_INCREMENT NOT NULL, tree_root INT DEFAULT NULL, parent_id INT DEFAULT NULL, name VARCHAR(50) NOT NULL, slug VARCHAR(50) NOT NULL, image LONGTEXT NOT NULL, description LONGTEXT NOT NULL, priority INT NOT NULL, metadesc VARCHAR(255) DEFAULT NULL, metakey VARCHAR(255) DEFAULT NULL, is_published TINYINT(1) DEFAULT \'0\' NOT NULL, is_checked_out TINYINT(1) DEFAULT \'0\' NOT NULL, lft INT NOT NULL, rgt INT NOT NULL, lvl INT NOT NULL, INDEX IDX_72113DE6A977936C (tree_root), INDEX IDX_72113DE6727ACA70 (parent_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE blog (id INT AUTO_INCREMENT NOT NULL, category_id INT NOT NULL, created_by INT NOT NULL, updated_by INT NOT NULL, name VARCHAR(60) NOT NULL, slug VARCHAR(60) NOT NULL, image VARCHAR(60) NOT NULL, summary LONGTEXT NOT NULL, description LONGTEXT NOT NULL, created_at DATETIME NOT NULL, updated_at DATETIME NOT NULL, INDEX IDX_C015514312469DE2 (category_id), INDEX IDX_C0155143DE12AB56 (created_by), INDEX IDX_C015514316FE72E1 (updated_by), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE blog_category ADD CONSTRAINT FK_72113DE6A977936C FOREIGN KEY (tree_root) REFERENCES category (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE blog_category ADD CONSTRAINT FK_72113DE6727ACA70 FOREIGN KEY (parent_id) REFERENCES category (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE blog ADD CONSTRAINT FK_C015514312469DE2 FOREIGN KEY (category_id) REFERENCES blog_category (id)');
        $this->addSql('ALTER TABLE blog ADD CONSTRAINT FK_C0155143DE12AB56 FOREIGN KEY (created_by) REFERENCES users (id)');
        $this->addSql('ALTER TABLE blog ADD CONSTRAINT FK_C015514316FE72E1 FOREIGN KEY (updated_by) REFERENCES users (id)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE blog DROP FOREIGN KEY FK_C015514312469DE2');
        $this->addSql('DROP TABLE blog_category');
        $this->addSql('DROP TABLE blog');
    }
}
