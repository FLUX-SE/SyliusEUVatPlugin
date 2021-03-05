<?php

declare(strict_types=1);

namespace FluxSE\SyliusEUVatPlugin\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20190507174705 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Add vat number field and channel related required fields.';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('ALTER TABLE sylius_address ADD vat_number VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE sylius_channel ADD base_country_id INT DEFAULT NULL, ADD european_zone_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE sylius_channel ADD CONSTRAINT FK_16C8119E6A95ED7C FOREIGN KEY (base_country_id) REFERENCES sylius_country (id) ON DELETE SET NULL');
        $this->addSql('ALTER TABLE sylius_channel ADD CONSTRAINT FK_16C8119E99F18829 FOREIGN KEY (european_zone_id) REFERENCES sylius_zone (id) ON DELETE SET NULL');
        $this->addSql('CREATE INDEX IDX_16C8119E6A95ED7C ON sylius_channel (base_country_id)');
        $this->addSql('CREATE INDEX IDX_16C8119E99F18829 ON sylius_channel (european_zone_id)');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE sylius_address DROP vat_number');
        $this->addSql('ALTER TABLE sylius_channel DROP FOREIGN KEY FK_16C8119E6A95ED7C');
        $this->addSql('ALTER TABLE sylius_channel DROP FOREIGN KEY FK_16C8119E99F18829');
        $this->addSql('DROP INDEX IDX_16C8119E6A95ED7C ON sylius_channel');
        $this->addSql('DROP INDEX IDX_16C8119E99F18829 ON sylius_channel');
        $this->addSql('ALTER TABLE sylius_channel DROP base_country_id, DROP european_zone_id');
    }
}
