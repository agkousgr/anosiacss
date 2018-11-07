<?php declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20181107164605 extends AbstractMigration
{
    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE anosia_search_keywords DROP FOREIGN KEY FK_5D659BC12469DE2');
        $this->addSql('DROP INDEX UNIQ_5D659BC12469DE2 ON anosia_search_keywords');
        $this->addSql('ALTER TABLE anosia_search_keywords CHANGE category_id category_id INT NOT NULL');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE anosia_search_keywords CHANGE category_id category_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE anosia_search_keywords ADD CONSTRAINT FK_5D659BC12469DE2 FOREIGN KEY (category_id) REFERENCES category (id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_5D659BC12469DE2 ON anosia_search_keywords (category_id)');
    }
}
