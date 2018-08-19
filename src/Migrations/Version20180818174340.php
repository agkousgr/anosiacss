<?php declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20180818174340 extends AbstractMigration
{
    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE admin_category (id INT AUTO_INCREMENT NOT NULL, tree_root INT DEFAULT NULL, parent_id INT DEFAULT NULL, created_by INT NOT NULL, updated_by INT NOT NULL, name VARCHAR(128) NOT NULL, slug VARCHAR(50) NOT NULL, description LONGTEXT DEFAULT NULL, priority INT NOT NULL, metadesc VARCHAR(255) DEFAULT NULL, metakey VARCHAR(255) DEFAULT NULL, is_published TINYINT(1) DEFAULT \'0\' NOT NULL, is_checked_out TINYINT(1) DEFAULT \'0\' NOT NULL, deleted_at DATETIME DEFAULT NULL, created_at DATETIME NOT NULL, updated_at DATETIME NOT NULL, lft INT NOT NULL, rgt INT NOT NULL, lvl INT NOT NULL, INDEX IDX_92EC1EC7A977936C (tree_root), INDEX IDX_92EC1EC7727ACA70 (parent_id), INDEX IDX_92EC1EC7DE12AB56 (created_by), INDEX IDX_92EC1EC716FE72E1 (updated_by), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE admin_category ADD CONSTRAINT FK_92EC1EC7A977936C FOREIGN KEY (tree_root) REFERENCES admin_category (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE admin_category ADD CONSTRAINT FK_92EC1EC7727ACA70 FOREIGN KEY (parent_id) REFERENCES admin_category (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE admin_category ADD CONSTRAINT FK_92EC1EC7DE12AB56 FOREIGN KEY (created_by) REFERENCES users (id)');
        $this->addSql('ALTER TABLE admin_category ADD CONSTRAINT FK_92EC1EC716FE72E1 FOREIGN KEY (updated_by) REFERENCES users (id)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE admin_category DROP FOREIGN KEY FK_92EC1EC7A977936C');
        $this->addSql('ALTER TABLE admin_category DROP FOREIGN KEY FK_92EC1EC7727ACA70');
        $this->addSql('DROP TABLE admin_category');
    }
}
