<?php declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20180702125146 extends AbstractMigration
{
    public function up(Schema $schema) : void
    {
        $this->addSql('ALTER TABLE article ADD image_size INT NOT NULL, CHANGE image image_name VARCHAR(255) NOT NULL');
    }

    public function down(Schema $schema) : void
    {
        $this->addSql('ALTER TABLE article DROP image_size, CHANGE image_name image VARCHAR(255) NOT NULL COLLATE utf8mb4_unicode_ci');
    }
}
