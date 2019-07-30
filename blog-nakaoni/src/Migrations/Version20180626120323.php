<?php declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20180626120323 extends AbstractMigration
{
    public function up(Schema $schema) : void
    {
        $this->addSql('
            CREATE TABLE article (
                id INT AUTO_INCREMENT NOT NULL,
                auteur_id INT NOT NULL,
                titre VARCHAR(255) NOT NULL,
                accroche VARCHAR(255) NOT NULL,
                texte LONGTEXT NOT NULL,
                categorie VARCHAR(255) NOT NULL,
                image VARCHAR(255) NOT NULL,
                youtube VARCHAR(255) DEFAULT NULL,
                public TINYINT(1) NOT NULL,
                nb_views INT NOT NULL,
                liked INT DEFAULT NULL,
                disliked INT DEFAULT NULL,
                comment_enabled TINYINT(1) NOT NULL,
                created_at DATETIME NOT NULL,
                updated_at DATETIME DEFAULT NULL,
                INDEX IDX_BFDD316860BB6FE6 (auteur_id),
                PRIMARY KEY(id)
            )
            DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB
        ');
        $this->addSql('
            CREATE TABLE commentaire (
                id INT AUTO_INCREMENT NOT NULL,
                utilisateur_id INT DEFAULT NULL,
                article_id INT DEFAULT NULL,
                referent_id INT DEFAULT NULL,
                texte LONGTEXT NOT NULL,
                statut TINYINT(1) NOT NULL,
                INDEX IDX_D9BEC0C41E969C5 (utilisateur_id),
                INDEX IDX_D9BEC0C41EBAF6CC (article_id),
                INDEX IDX_D9BEC0C435E47E35 (referent_id),
                PRIMARY KEY(id)
            )
            DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB
        ');
        $this->addSql('
            CREATE TABLE utilisateur (
                id INT AUTO_INCREMENT NOT NULL,
                username VARCHAR(180) NOT NULL,
                username_canonical VARCHAR(180) NOT NULL,
                email VARCHAR(180) NOT NULL,
                email_canonical VARCHAR(180) NOT NULL,
                enabled TINYINT(1) NOT NULL,
                salt VARCHAR(255) DEFAULT NULL,
                password VARCHAR(255) NOT NULL,
                last_login DATETIME DEFAULT NULL,
                confirmation_token VARCHAR(180) DEFAULT NULL,
                password_requested_at DATETIME DEFAULT NULL,
                roles LONGTEXT NOT NULL COMMENT \'(DC2Type:array)\',
                pseudo VARCHAR(255) NOT NULL,
                statut SMALLINT NOT NULL,
                UNIQUE INDEX UNIQ_497B315E92FC23A8 (username_canonical),
                UNIQUE INDEX UNIQ_497B315EA0D96FBF (email_canonical),
                UNIQUE INDEX UNIQ_497B315EC05FB297 (confirmation_token),
                PRIMARY KEY(id)
            )
            DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB
        ');
        $this->addSql('
            ALTER TABLE article
            ADD CONSTRAINT FK_BFDD316860BB6FE6
            FOREIGN KEY (auteur_id)
            REFERENCES utilisateur (id)
        ');
        $this->addSql('
            ALTER TABLE commentaire
            ADD CONSTRAINT FK_D9BEC0C41E969C5
            FOREIGN KEY (utilisateur_id)
            REFERENCES utilisateur (id)
        ');
        $this->addSql('
            ALTER TABLE commentaire
            ADD CONSTRAINT FK_D9BEC0C41EBAF6CC
            FOREIGN KEY (article_id)
            REFERENCES article (id)
        ');
        $this->addSql('
            ALTER TABLE commentaire
            ADD CONSTRAINT FK_D9BEC0C435E47E35
            FOREIGN KEY (referent_id)
            REFERENCES commentaire (id)
        ');
    }

    public function down(Schema $schema) : void
    {
        $this->addSql('ALTER TABLE commentaire DROP FOREIGN KEY FK_D9BEC0C41EBAF6CC');
        $this->addSql('ALTER TABLE commentaire DROP FOREIGN KEY FK_D9BEC0C435E47E35');
        $this->addSql('ALTER TABLE article DROP FOREIGN KEY FK_BFDD316860BB6FE6');
        $this->addSql('ALTER TABLE commentaire DROP FOREIGN KEY FK_D9BEC0C41E969C5');
        $this->addSql('DROP TABLE article');
        $this->addSql('DROP TABLE commentaire');
        $this->addSql('DROP TABLE utilisateur');
    }
}
