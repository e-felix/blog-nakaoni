<?php declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20180628131133 extends AbstractMigration
{
    public function up(Schema $schema) : void
    {
        $this->addSql('ALTER TABLE utilisateur DROP pseudo, DROP statut');
    }

    public function down(Schema $schema) : void
    {
        $this->addSql('ALTER TABLE utilisateur ADD pseudo VARCHAR(255) NOT NULL COLLATE utf8mb4_unicode_ci, ADD statut SMALLINT NOT NULL');
    }
}
