<?php declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20180811221111 extends AbstractMigration
{
    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE slider ADD description LONGTEXT NOT NULL, ADD is_published TINYINT(1) NOT NULL, DROP slug, CHANGE image image LONGTEXT NOT NULL, CHANGE summary url LONGTEXT NOT NULL');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE slider ADD slug VARCHAR(60) NOT NULL COLLATE utf8mb4_unicode_ci, ADD summary LONGTEXT NOT NULL COLLATE utf8mb4_unicode_ci, DROP url, DROP description, DROP is_published, CHANGE image image VARCHAR(60) NOT NULL COLLATE utf8mb4_unicode_ci');
    }
}
