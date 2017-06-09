<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20170609163847 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE moduleInfo (id BIGINT NOT NULL, name VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE modules ADD module_info_id BIGINT NOT NULL');
        $this->addSql('ALTER TABLE modules ADD CONSTRAINT FK_2EB743D7F6F0FFE1 FOREIGN KEY (module_info_id) REFERENCES moduleInfo (id)');
        $this->addSql('CREATE INDEX IDX_2EB743D7F6F0FFE1 ON modules (module_info_id)');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE modules DROP FOREIGN KEY FK_2EB743D7F6F0FFE1');
        $this->addSql('DROP TABLE moduleInfo');
        $this->addSql('DROP INDEX IDX_2EB743D7F6F0FFE1 ON modules');
        $this->addSql('ALTER TABLE modules DROP module_info_id');
    }
}
