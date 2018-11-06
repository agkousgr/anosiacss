<?php declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20181106105853 extends AbstractMigration
{
    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE also_viewed_products (id INT AUTO_INCREMENT NOT NULL, product_id INT NOT NULL, also_viewed_id INT NOT NULL, views INT NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE pireaus_results (id INT AUTO_INCREMENT NOT NULL, cliend_id INT NOT NULL, support_reference_id INT NOT NULL, merchant_reference VARCHAR(50) NOT NULL, status_flag VARCHAR(12) DEFAULT NULL, response_code VARCHAR(2) DEFAULT NULL, response_description LONGTEXT DEFAULT NULL, result_code VARCHAR(5) DEFAULT NULL, result_description LONGTEXT DEFAULT NULL, approval_code VARCHAR(6) NOT NULL, package_no INT DEFAULT NULL, auth_status VARCHAR(2) DEFAULT NULL, created_at DATETIME NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE articles CHANGE updated_by updated_by INT NOT NULL, CHANGE views views INT DEFAULT NULL');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('DROP TABLE also_viewed_products');
        $this->addSql('DROP TABLE pireaus_results');
        $this->addSql('ALTER TABLE articles CHANGE updated_by updated_by INT DEFAULT NULL, CHANGE views views INT DEFAULT 0');
    }
}
