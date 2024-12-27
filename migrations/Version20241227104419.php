<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20241227104419 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE INDEX idx_categories_created ON categories (created_at)');
        $this->addSql('ALTER TABLE comments ALTER deleted_at TYPE TIMESTAMP(0) WITHOUT TIME ZONE');
        $this->addSql('COMMENT ON COLUMN comments.deleted_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('CREATE INDEX idx_comments_created ON comments (created_at)');
        $this->addSql('CREATE INDEX idx_comments_deleted ON comments (deleted_at)');
        $this->addSql('ALTER TABLE news ALTER picture TYPE VARCHAR(512)');
        $this->addSql('ALTER TABLE news ALTER deleted_at TYPE TIMESTAMP(0) WITHOUT TIME ZONE');
        $this->addSql('COMMENT ON COLUMN news.deleted_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('CREATE INDEX idx_news_title ON news (title)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP INDEX idx_comments_created');
        $this->addSql('DROP INDEX idx_comments_deleted');
        $this->addSql('ALTER TABLE comments ALTER deleted_at TYPE DATE');
        $this->addSql('COMMENT ON COLUMN comments.deleted_at IS \'(DC2Type:date_immutable)\'');
        $this->addSql('DROP INDEX idx_categories_created');
        $this->addSql('DROP INDEX idx_news_title');
        $this->addSql('ALTER TABLE news ALTER picture TYPE VARCHAR(255)');
        $this->addSql('ALTER TABLE news ALTER deleted_at TYPE DATE');
        $this->addSql('COMMENT ON COLUMN news.deleted_at IS \'(DC2Type:date_immutable)\'');
    }
}
