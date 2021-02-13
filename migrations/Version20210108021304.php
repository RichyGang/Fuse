<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20210108021304 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE proposal (id INT AUTO_INCREMENT NOT NULL, ressource_id INT NOT NULL, author_id INT NOT NULL, quantity VARCHAR(50) NOT NULL, unity VARCHAR(50) DEFAULT NULL, location LONGTEXT NOT NULL, start DATE NOT NULL, deadline DATE NOT NULL, need_or_ask TINYINT(1) NOT NULL, create_at DATETIME NOT NULL, done TINYINT(1) DEFAULT \'0\' NOT NULL, INDEX IDX_BFE59472FC6CD52A (ressource_id), INDEX IDX_BFE59472F675F31B (author_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE users (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, email VARCHAR(255) NOT NULL, password VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE proposal ADD CONSTRAINT FK_BFE59472FC6CD52A FOREIGN KEY (ressource_id) REFERENCES ressource (id)');
        $this->addSql('ALTER TABLE proposal ADD CONSTRAINT FK_BFE59472F675F31B FOREIGN KEY (author_id) REFERENCES users (id)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE proposal DROP FOREIGN KEY FK_BFE59472F675F31B');
        $this->addSql('DROP TABLE proposal');
        $this->addSql('DROP TABLE users');
    }
}
