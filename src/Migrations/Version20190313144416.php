<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20190313144416 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE migration_products ADD availability VARCHAR(1) DEFAULT NULL, CHANGE web_visible web_visible VARCHAR(1) DEFAULT NULL, CHANGE category_ids category_ids VARCHAR(50) DEFAULT NULL, CHANGE retail_price retail_price NUMERIC(7, 2) DEFAULT NULL, CHANGE web_price web_price NUMERIC(7, 2) DEFAULT NULL');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE migration_products DROP availability, CHANGE retail_price retail_price NUMERIC(7, 2) NOT NULL, CHANGE web_price web_price NUMERIC(7, 2) NOT NULL, CHANGE web_visible web_visible VARCHAR(1) NOT NULL COLLATE utf8mb4_general_ci, CHANGE category_ids category_ids VARCHAR(50) NOT NULL COLLATE utf8mb4_general_ci');
    }
}
