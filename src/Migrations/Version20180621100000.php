<?php declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20180621100000 extends AbstractMigration
{
    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE address (id INT AUTO_INCREMENT NOT NULL, line1 VARCHAR(255) NOT NULL, line2 VARCHAR(255) DEFAULT NULL, city VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE baccalaureate (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE country (id INT AUTO_INCREMENT NOT NULL, code VARCHAR(2) NOT NULL, en_name VARCHAR(45) NOT NULL, fr_name VARCHAR(45) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE university (id INT AUTO_INCREMENT NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE user (id INT AUTO_INCREMENT NOT NULL, baccalaureate_id INT DEFAULT NULL, sponsor_id INT DEFAULT NULL, user_name VARCHAR(255) NOT NULL, first_name VARCHAR(255) NOT NULL, last_name VARCHAR(255) NOT NULL, password VARCHAR(255) NOT NULL, salt VARCHAR(255) NOT NULL, birth_day DATETIME NOT NULL, role JSON NOT NULL, INDEX IDX_8D93D649EACBD8D8 (baccalaureate_id), INDEX IDX_8D93D649FA632810 (sponsor_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE user_university (user_id INT NOT NULL, university_id INT NOT NULL, INDEX IDX_71D4300A76ED395 (user_id), INDEX IDX_71D4300309D1878 (university_id), PRIMARY KEY(user_id, university_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE user ADD CONSTRAINT FK_8D93D649EACBD8D8 FOREIGN KEY (baccalaureate_id) REFERENCES baccalaureate (id)');
        $this->addSql('ALTER TABLE user ADD CONSTRAINT FK_8D93D649FA632810 FOREIGN KEY (sponsor_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE user_university ADD CONSTRAINT FK_71D4300A76ED395 FOREIGN KEY (user_id) REFERENCES user (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE user_university ADD CONSTRAINT FK_71D4300309D1878 FOREIGN KEY (university_id) REFERENCES university (id) ON DELETE CASCADE');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE user DROP FOREIGN KEY FK_8D93D649EACBD8D8');
        $this->addSql('ALTER TABLE user_university DROP FOREIGN KEY FK_71D4300309D1878');
        $this->addSql('ALTER TABLE user DROP FOREIGN KEY FK_8D93D649FA632810');
        $this->addSql('ALTER TABLE user_university DROP FOREIGN KEY FK_71D4300A76ED395');
        $this->addSql('DROP TABLE address');
        $this->addSql('DROP TABLE baccalaureate');
        $this->addSql('DROP TABLE country');
        $this->addSql('DROP TABLE university');
        $this->addSql('DROP TABLE user');
        $this->addSql('DROP TABLE user_university');
    }
}
