<?php declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20181104113511 extends AbstractMigration
{
    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE anosia_search_keywords (id INT AUTO_INCREMENT NOT NULL, category_id INT DEFAULT NULL, keyword VARCHAR(100) NOT NULL, UNIQUE INDEX UNIQ_5D659BC12469DE2 (category_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE anosia_search_keywords ADD CONSTRAINT FK_5D659BC12469DE2 FOREIGN KEY (category_id) REFERENCES category (id)');
        $this->addSql('ALTER TABLE category ADD items_count INT NOT NULL');
        $this->addSql('ALTER TABLE articles CHANGE name name VARCHAR(250) NOT NULL, CHANGE slug slug VARCHAR(250) NOT NULL, CHANGE image image VARCHAR(250) DEFAULT NULL, CHANGE updated_at updated_at DATETIME DEFAULT NULL');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('DROP TABLE anosia_search_keywords');
        $this->addSql('ALTER TABLE articles CHANGE name name VARCHAR(60) NOT NULL COLLATE utf8mb4_unicode_ci, CHANGE slug slug VARCHAR(60) NOT NULL COLLATE utf8mb4_unicode_ci, CHANGE image image VARCHAR(60) DEFAULT NULL COLLATE utf8mb4_unicode_ci, CHANGE updated_at updated_at DATETIME NOT NULL');
        $this->addSql('ALTER TABLE category DROP items_count');
    }
}
