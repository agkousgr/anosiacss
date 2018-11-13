<?php declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20181111165224 extends AbstractMigration
{
    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE migration_products (id INT AUTO_INCREMENT NOT NULL, s1id INT NOT NULL, name LONGTEXT NOT NULL, sku VARCHAR(100) NOT NULL, small_description LONGTEXT DEFAULT NULL, large_description LONGTEXT DEFAULT NULL, ingredients LONGTEXT DEFAULT NULL, instructions LONGTEXT DEFAULT NULL, old_slug VARCHAR(250) DEFAULT NULL, slug VARCHAR(250) NOT NULL, seo_title LONGTEXT DEFAULT NULL, seo_keywords LONGTEXT DEFAULT NULL, manufacturer VARCHAR(100) DEFAULT NULL, images LONGTEXT DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('DROP TABLE migration_products');
    }
}
