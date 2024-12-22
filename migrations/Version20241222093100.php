<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20241222093100 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE news (id UUID NOT NULL, author_id UUID NOT NULL, title VARCHAR(255) NOT NULL, short_description TEXT NOT NULL, content TEXT NOT NULL, picture VARCHAR(255) DEFAULT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT CURRENT_TIMESTAMP NOT NULL, updated_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_1DD39950F675F31B ON news (author_id)');
        $this->addSql('COMMENT ON COLUMN news.id IS \'(DC2Type:news_id)\'');
        $this->addSql('COMMENT ON COLUMN news.author_id IS \'(DC2Type:user_id)\'');
        $this->addSql('COMMENT ON COLUMN news.created_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('COMMENT ON COLUMN news.updated_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('CREATE TABLE news_categories (news_id UUID NOT NULL, category_id UUID NOT NULL, PRIMARY KEY(news_id, category_id))');
        $this->addSql('CREATE INDEX IDX_D68C9111B5A459A0 ON news_categories (news_id)');
        $this->addSql('CREATE INDEX IDX_D68C911112469DE2 ON news_categories (category_id)');
        $this->addSql('COMMENT ON COLUMN news_categories.news_id IS \'(DC2Type:news_id)\'');
        $this->addSql('COMMENT ON COLUMN news_categories.category_id IS \'(DC2Type:category_id)\'');
        $this->addSql('ALTER TABLE news ADD CONSTRAINT FK_1DD39950F675F31B FOREIGN KEY (author_id) REFERENCES users (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE news_categories ADD CONSTRAINT FK_D68C9111B5A459A0 FOREIGN KEY (news_id) REFERENCES news (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE news_categories ADD CONSTRAINT FK_D68C911112469DE2 FOREIGN KEY (category_id) REFERENCES categories (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE news DROP CONSTRAINT FK_1DD39950F675F31B');
        $this->addSql('ALTER TABLE news_categories DROP CONSTRAINT FK_D68C9111B5A459A0');
        $this->addSql('ALTER TABLE news_categories DROP CONSTRAINT FK_D68C911112469DE2');
        $this->addSql('DROP TABLE news');
        $this->addSql('DROP TABLE news_categories');
    }
}
