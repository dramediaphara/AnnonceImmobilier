<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20220530152009 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE chambre_tag (chambre_id INT NOT NULL, tag_id INT NOT NULL, INDEX IDX_4C05D829B177F54 (chambre_id), INDEX IDX_4C05D82BAD26311 (tag_id), PRIMARY KEY(chambre_id, tag_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE tag (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE chambre_tag ADD CONSTRAINT FK_4C05D829B177F54 FOREIGN KEY (chambre_id) REFERENCES chambre (id)');
        $this->addSql('ALTER TABLE chambre_tag ADD CONSTRAINT FK_4C05D82BAD26311 FOREIGN KEY (tag_id) REFERENCES tag (id) ON DELETE CASCADE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE chambre_tag DROP FOREIGN KEY FK_4C05D82BAD26311');
        $this->addSql('DROP TABLE chambre_tag');
        $this->addSql('DROP TABLE tag');
    }
}
