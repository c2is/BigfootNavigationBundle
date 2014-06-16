<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Migration introduced in BigfootNavigatioBundle v2.2.x
 */
class Version2_2_6 extends AbstractMigration
{
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != "mysql", "Migration can only be executed safely on 'mysql'.");

        $this->addSql("UPDATE ext_translations set field = 'label' where object_class = 'Bigfoot\\\\Bundle\\\\NavigationBundle\\\\Entity\\\\Menu\\\\Item' and field = 'name'");
        $this->addSql("ALTER TABLE bigfoot_menu_item change name label varchar(255) default null");
        $this->addSql("ALTER TABLE bigfoot_menu_item ADD `name` VARCHAR(255) default null after label");
        $this->addSql("UPDATE bigfoot_menu_item set name = label");
    }

    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != "mysql", "Migration can only be executed safely on 'mysql'.");

        $this->addSql("UPDATE ext_translations set field = 'name' where object_class = 'Bigfoot\\\\Bundle\\\\NavigationBundle\\\\Entity\\\\Menu\\\\Item' and field = 'label'");
        $this->addSql("ALTER TABLE bigfoot_menu_item DROP column `name`");
        $this->addSql("ALTER TABLE bigfoot_menu_item change label name varchar(255) default null");
    }
}
