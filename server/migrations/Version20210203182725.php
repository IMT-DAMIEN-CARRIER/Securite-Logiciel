<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20210203182725 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP TABLE permission_libelle_permission');
        $this->addSql('ALTER TABLE permission ADD libelle_permission_id INT NOT NULL');
        $this->addSql(
            'ALTER TABLE permission ADD CONSTRAINT FK_E04992AA578227A7 FOREIGN KEY (libelle_permission_id) REFERENCES libelle_permission (id)'
        );
        $this->addSql('CREATE INDEX IDX_E04992AA578227A7 ON permission (libelle_permission_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql(
            'CREATE TABLE permission_libelle_permission (permission_id INT NOT NULL, libelle_permission_id INT NOT NULL, INDEX IDX_9621E971578227A7 (libelle_permission_id), INDEX IDX_9621E971FED90CCA (permission_id), PRIMARY KEY(permission_id, libelle_permission_id)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB COMMENT = \'\' '
        );
        $this->addSql(
            'ALTER TABLE permission_libelle_permission ADD CONSTRAINT FK_9621E971578227A7 FOREIGN KEY (libelle_permission_id) REFERENCES libelle_permission (id) ON DELETE CASCADE'
        );
        $this->addSql(
            'ALTER TABLE permission_libelle_permission ADD CONSTRAINT FK_9621E971FED90CCA FOREIGN KEY (permission_id) REFERENCES permission (id) ON DELETE CASCADE'
        );
        $this->addSql('ALTER TABLE permission DROP FOREIGN KEY FK_E04992AA578227A7');
        $this->addSql('DROP INDEX IDX_E04992AA578227A7 ON permission');
        $this->addSql('ALTER TABLE permission DROP libelle_permission_id');
    }
}
