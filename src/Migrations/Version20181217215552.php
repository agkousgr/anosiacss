<?php declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20181217215552 extends AbstractMigration
{
    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE migration_products ADD barcode VARCHAR(50) DEFAULT NULL, ADD retail_price NUMERIC(7, 2) NOT NULL, ADD web_price NUMERIC(7, 2) NOT NULL, ADD discount INT DEFAULT NULL, ADD manufacturer_id INT DEFAULT NULL, ADD web_visible VARCHAR(1) DEFAULT NULL, ADD category_ids VARCHAR(50) NOT NULL');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE migration_products DROP barcode, DROP retail_price, DROP web_price, DROP discount, DROP manufacturer_id, DROP web_visible, DROP category_ids');
    }
}
