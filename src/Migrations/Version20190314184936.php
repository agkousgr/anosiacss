<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20190314184936 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE category_children (parent_id INT NOT NULL, child_id INT NOT NULL, INDEX IDX_16ED35C1727ACA70 (parent_id), INDEX IDX_16ED35C1DD62C21B (child_id), PRIMARY KEY(parent_id, child_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE category_children ADD CONSTRAINT FK_16ED35C1727ACA70 FOREIGN KEY (parent_id) REFERENCES category (id)');
        $this->addSql('ALTER TABLE category_children ADD CONSTRAINT FK_16ED35C1DD62C21B FOREIGN KEY (child_id) REFERENCES category (id)');
        $this->addSql('ALTER TABLE category DROP FOREIGN KEY FK_64C19C1727ACA70');
        $this->addSql('ALTER TABLE category DROP FOREIGN KEY FK_64C19C1A977936C');
        $this->addSql('DROP INDEX IDX_64C19C1A977936C ON category');
        $this->addSql('DROP INDEX IDX_64C19C1727ACA70 ON category');
        $this->addSql('ALTER TABLE category ADD s1level INT DEFAULT NULL, DROP tree_root, DROP parent_id, DROP lft, DROP rgt, DROP lvl, CHANGE id id INT AUTO_INCREMENT NOT NULL, CHANGE image_url image_url LONGTEXT DEFAULT NULL, CHANGE description description LONGTEXT DEFAULT NULL, CHANGE priority priority INT NOT NULL');
        $this->addSql('CREATE INDEX SoftOne_ID_Index ON category (s1id)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('DROP TABLE category_children');
        $this->addSql('DROP INDEX SoftOne_ID_Index ON category');
        $this->addSql('ALTER TABLE category ADD parent_id INT DEFAULT NULL, ADD lft INT NOT NULL, ADD rgt INT NOT NULL, ADD lvl INT NOT NULL, CHANGE id id INT NOT NULL, CHANGE image_url image_url LONGTEXT NOT NULL COLLATE utf8mb4_unicode_ci, CHANGE description description LONGTEXT NOT NULL COLLATE utf8mb4_unicode_ci, CHANGE priority priority NUMERIC(4, 0) NOT NULL, CHANGE s1level tree_root INT DEFAULT NULL');
        $this->addSql('ALTER TABLE category ADD CONSTRAINT FK_64C19C1727ACA70 FOREIGN KEY (parent_id) REFERENCES category (id) ON UPDATE NO ACTION ON DELETE CASCADE');
        $this->addSql('ALTER TABLE category ADD CONSTRAINT FK_64C19C1A977936C FOREIGN KEY (tree_root) REFERENCES category (id) ON UPDATE NO ACTION ON DELETE CASCADE');
        $this->addSql('CREATE INDEX IDX_64C19C1A977936C ON category (tree_root)');
        $this->addSql('CREATE INDEX IDX_64C19C1727ACA70 ON category (parent_id)');
    }
}
