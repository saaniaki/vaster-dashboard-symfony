<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20170716114411 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE modules (id BIGINT AUTO_INCREMENT NOT NULL, page_id BIGINT NOT NULL, module_info_id BIGINT NOT NULL, size INT NOT NULL, rank INT NOT NULL, analytics VARCHAR(255) DEFAULT NULL, user_type LONGTEXT DEFAULT NULL COMMENT \'(DC2Type:json_array)\', availability VARCHAR(255) DEFAULT NULL, configuration LONGTEXT DEFAULT NULL COMMENT \'(DC2Type:json_array)\', device_type VARCHAR(255) DEFAULT NULL, keyword VARCHAR(255) DEFAULT NULL, from_date DATETIME DEFAULT NULL, to_date DATETIME DEFAULT NULL, INDEX IDX_2EB743D7C4663E4 (page_id), INDEX IDX_2EB743D7F6F0FFE1 (module_info_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE pages (id BIGINT AUTO_INCREMENT NOT NULL, user_id BIGINT NOT NULL, rank INT NOT NULL, name VARCHAR(255) NOT NULL, INDEX IDX_2074E575A76ED395 (user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE users (id BIGINT NOT NULL, email VARCHAR(255) NOT NULL, password VARCHAR(255) NOT NULL, roles LONGTEXT NOT NULL COMMENT \'(DC2Type:json_array)\', UNIQUE INDEX UNIQ_1483A5E9E7927C74 (email), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE moduleInfo (id BIGINT NOT NULL, name VARCHAR(255) NOT NULL, type VARCHAR(255) NOT NULL, description LONGTEXT NOT NULL, guide LONGTEXT NOT NULL, available_analytics LONGTEXT NOT NULL COMMENT \'(DC2Type:json_array)\', available_configuration LONGTEXT NOT NULL COMMENT \'(DC2Type:json_array)\', PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE modules ADD CONSTRAINT FK_2EB743D7C4663E4 FOREIGN KEY (page_id) REFERENCES pages (id)');
        $this->addSql('ALTER TABLE modules ADD CONSTRAINT FK_2EB743D7F6F0FFE1 FOREIGN KEY (module_info_id) REFERENCES moduleInfo (id)');
        $this->addSql('ALTER TABLE pages ADD CONSTRAINT FK_2074E575A76ED395 FOREIGN KEY (user_id) REFERENCES users (id)');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE modules DROP FOREIGN KEY FK_2EB743D7C4663E4');
        $this->addSql('ALTER TABLE pages DROP FOREIGN KEY FK_2074E575A76ED395');
        $this->addSql('ALTER TABLE modules DROP FOREIGN KEY FK_2EB743D7F6F0FFE1');
        $this->addSql('DROP TABLE modules');
        $this->addSql('DROP TABLE pages');
        $this->addSql('DROP TABLE users');
        $this->addSql('DROP TABLE moduleInfo');
    }
}
