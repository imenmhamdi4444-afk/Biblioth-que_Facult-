<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20261122195020 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE author (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, surname VARCHAR(255) NOT NULL, work_address VARCHAR(255) DEFAULT NULL, interest1 VARCHAR(255) DEFAULT NULL, interest2 VARCHAR(255) DEFAULT NULL, interest3 VARCHAR(255) DEFAULT NULL, origin VARCHAR(255) DEFAULT NULL, UNIQUE INDEX unique_author_name (name, surname), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE book (id INT AUTO_INCREMENT NOT NULL, publisher_id INT DEFAULT NULL, code VARCHAR(100) NOT NULL, title VARCHAR(255) NOT NULL, publication_date DATE DEFAULT NULL, nb_copies_acquired INT NOT NULL, nb_copies_borrowed INT DEFAULT 0 NOT NULL, keywords VARCHAR(500) DEFAULT NULL, image VARCHAR(255) DEFAULT NULL, UNIQUE INDEX UNIQ_CBE5A33177153098 (code), INDEX IDX_CBE5A33140C86FCE (publisher_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE book_author (book_id INT NOT NULL, author_id INT NOT NULL, INDEX IDX_9478D34516A2B381 (book_id), INDEX IDX_9478D345F675F31B (author_id), PRIMARY KEY(book_id, author_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE borrowing (id INT AUTO_INCREMENT NOT NULL, student_id INT NOT NULL, book_id INT DEFAULT NULL, revue_id INT DEFAULT NULL, date_borrowed DATETIME NOT NULL, book_returned TINYINT(1) NOT NULL, date_due DATETIME DEFAULT NULL, date_returned DATETIME DEFAULT NULL, INDEX IDX_226E5897CB944F1A (student_id), INDEX IDX_226E589716A2B381 (book_id), INDEX IDX_226E58972B68B0B6 (revue_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE publisher (id INT AUTO_INCREMENT NOT NULL, raison_sociale VARCHAR(255) NOT NULL, address VARCHAR(255) NOT NULL, director_name VARCHAR(255) NOT NULL, commercial_contact VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE revue (id INT AUTO_INCREMENT NOT NULL, publisher_id INT DEFAULT NULL, code VARCHAR(100) NOT NULL, title VARCHAR(255) NOT NULL, publication_date DATE DEFAULT NULL, nb_copies_acquired INT NOT NULL, nb_copies_borrowed INT DEFAULT 0 NOT NULL, keywords VARCHAR(500) DEFAULT NULL, periodicity VARCHAR(100) DEFAULT NULL, subscription_date DATE DEFAULT NULL, subscription_amount NUMERIC(10, 2) DEFAULT NULL, image VARCHAR(255) DEFAULT NULL, UNIQUE INDEX UNIQ_76244F0577153098 (code), INDEX IDX_76244F0540C86FCE (publisher_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE revue_author (revue_id INT NOT NULL, author_id INT NOT NULL, INDEX IDX_92BFDED12B68B0B6 (revue_id), INDEX IDX_92BFDED1F675F31B (author_id), PRIMARY KEY(revue_id, author_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE student (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, surname VARCHAR(255) NOT NULL, matricule VARCHAR(50) DEFAULT NULL, email VARCHAR(255) DEFAULT NULL, department VARCHAR(100) DEFAULT NULL, UNIQUE INDEX UNIQ_B723AF3312B2DC9C (matricule), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE user (id INT AUTO_INCREMENT NOT NULL, student_id INT DEFAULT NULL, username VARCHAR(180) NOT NULL, email VARCHAR(255) NOT NULL, first_name VARCHAR(100) DEFAULT NULL, last_name VARCHAR(100) DEFAULT NULL, roles JSON NOT NULL, password VARCHAR(255) NOT NULL, UNIQUE INDEX UNIQ_8D93D649F85E0677 (username), UNIQUE INDEX UNIQ_8D93D649E7927C74 (email), UNIQUE INDEX UNIQ_8D93D649CB944F1A (student_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE book ADD CONSTRAINT FK_CBE5A33140C86FCE FOREIGN KEY (publisher_id) REFERENCES publisher (id)');
        $this->addSql('ALTER TABLE book_author ADD CONSTRAINT FK_9478D34516A2B381 FOREIGN KEY (book_id) REFERENCES book (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE book_author ADD CONSTRAINT FK_9478D345F675F31B FOREIGN KEY (author_id) REFERENCES author (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE borrowing ADD CONSTRAINT FK_226E5897CB944F1A FOREIGN KEY (student_id) REFERENCES student (id)');
        $this->addSql('ALTER TABLE borrowing ADD CONSTRAINT FK_226E589716A2B381 FOREIGN KEY (book_id) REFERENCES book (id)');
        $this->addSql('ALTER TABLE borrowing ADD CONSTRAINT FK_226E58972B68B0B6 FOREIGN KEY (revue_id) REFERENCES revue (id)');
        $this->addSql('ALTER TABLE revue ADD CONSTRAINT FK_76244F0540C86FCE FOREIGN KEY (publisher_id) REFERENCES publisher (id)');
        $this->addSql('ALTER TABLE revue_author ADD CONSTRAINT FK_92BFDED12B68B0B6 FOREIGN KEY (revue_id) REFERENCES revue (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE revue_author ADD CONSTRAINT FK_92BFDED1F675F31B FOREIGN KEY (author_id) REFERENCES author (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE user ADD CONSTRAINT FK_8D93D649CB944F1A FOREIGN KEY (student_id) REFERENCES student (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE book DROP FOREIGN KEY FK_CBE5A33140C86FCE');
        $this->addSql('ALTER TABLE book_author DROP FOREIGN KEY FK_9478D34516A2B381');
        $this->addSql('ALTER TABLE book_author DROP FOREIGN KEY FK_9478D345F675F31B');
        $this->addSql('ALTER TABLE borrowing DROP FOREIGN KEY FK_226E5897CB944F1A');
        $this->addSql('ALTER TABLE borrowing DROP FOREIGN KEY FK_226E589716A2B381');
        $this->addSql('ALTER TABLE borrowing DROP FOREIGN KEY FK_226E58972B68B0B6');
        $this->addSql('ALTER TABLE revue DROP FOREIGN KEY FK_76244F0540C86FCE');
        $this->addSql('ALTER TABLE revue_author DROP FOREIGN KEY FK_92BFDED12B68B0B6');
        $this->addSql('ALTER TABLE revue_author DROP FOREIGN KEY FK_92BFDED1F675F31B');
        $this->addSql('ALTER TABLE user DROP FOREIGN KEY FK_8D93D649CB944F1A');
        $this->addSql('DROP TABLE author');
        $this->addSql('DROP TABLE book');
        $this->addSql('DROP TABLE book_author');
        $this->addSql('DROP TABLE borrowing');
        $this->addSql('DROP TABLE publisher');
        $this->addSql('DROP TABLE revue');
        $this->addSql('DROP TABLE revue_author');
        $this->addSql('DROP TABLE student');
        $this->addSql('DROP TABLE user');
    }
}
