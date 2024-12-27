<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20241227102828 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE INDEX idx_categories_title ON categories (title)');
        $this->addSql('CREATE INDEX idx_categories_deleted ON categories (deleted_at)');
        $this->addSql('CREATE INDEX idx_news_created ON news (created_at)');
        $this->addSql('CREATE INDEX idx_news_deleted ON news (deleted_at)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP INDEX idx_categories_title');
        $this->addSql('DROP INDEX idx_categories_deleted');
        $this->addSql('DROP INDEX idx_news_created');
        $this->addSql('DROP INDEX idx_news_deleted');
    }
}
