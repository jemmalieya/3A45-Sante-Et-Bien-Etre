<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20260201152419 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE fiche_medicale (id INT AUTO_INCREMENT NOT NULL, diagnostic LONGTEXT NOT NULL, observations LONGTEXT DEFAULT NULL, resultats_examens LONGTEXT DEFAULT NULL, start_time DATETIME NOT NULL, end_time DATETIME DEFAULT NULL, duree_minutes INT DEFAULT NULL, created_at DATETIME NOT NULL, rendez_vous_id INT DEFAULT NULL, UNIQUE INDEX UNIQ_20D232691EF7EAA (rendez_vous_id), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci`');
        $this->addSql('ALTER TABLE fiche_medicale ADD CONSTRAINT FK_20D232691EF7EAA FOREIGN KEY (rendez_vous_id) REFERENCES rendez_vous (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE fiche_medicale DROP FOREIGN KEY FK_20D232691EF7EAA');
        $this->addSql('DROP TABLE fiche_medicale');
    }
}
