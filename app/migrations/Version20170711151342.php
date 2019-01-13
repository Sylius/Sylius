<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Sylius\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;
use Doctrine\Migrations\Exception\SkipMigration;

class Version20170711151342 extends AbstractMigration
{
    public function up(Schema $schema): void
    {
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        // Check if foreign key with correct name exists. If so, this indicates all other foreign keys are correct as well
        foreach ($schema->getTable('sylius_admin_api_access_token')->getForeignKeys() as $key) {
            if ($key->getName() === 'FK_2AA4915D19EB6921') {
                throw new SkipMigration('Database has correct index name');
            }
        }

        $this->addSql('ALTER TABLE sylius_admin_api_access_token DROP FOREIGN KEY FK_7D83AA7F19EB6921');
        $this->addSql('ALTER TABLE sylius_admin_api_access_token DROP FOREIGN KEY FK_7D83AA7FA76ED395');
        $this->addSql('ALTER TABLE sylius_admin_api_access_token ADD CONSTRAINT FK_2AA4915D19EB6921 FOREIGN KEY (client_id) REFERENCES sylius_admin_api_client (id)');
        $this->addSql('ALTER TABLE sylius_admin_api_access_token ADD CONSTRAINT FK_2AA4915DA76ED395 FOREIGN KEY (user_id) REFERENCES sylius_admin_user (id)');
        $this->addSql('ALTER TABLE sylius_admin_api_auth_code DROP FOREIGN KEY FK_C840417919EB6921');
        $this->addSql('ALTER TABLE sylius_admin_api_auth_code DROP FOREIGN KEY FK_C8404179A76ED395');
        $this->addSql('ALTER TABLE sylius_admin_api_auth_code ADD CONSTRAINT FK_E366D84819EB6921 FOREIGN KEY (client_id) REFERENCES sylius_admin_api_client (id)');
        $this->addSql('ALTER TABLE sylius_admin_api_auth_code ADD CONSTRAINT FK_E366D848A76ED395 FOREIGN KEY (user_id) REFERENCES sylius_admin_user (id)');
        $this->addSql('ALTER TABLE sylius_admin_api_refresh_token DROP FOREIGN KEY FK_4457852519EB6921');
        $this->addSql('ALTER TABLE sylius_admin_api_refresh_token DROP FOREIGN KEY FK_44578525A76ED395');
        $this->addSql('ALTER TABLE sylius_admin_api_refresh_token ADD CONSTRAINT FK_9160E3FA19EB6921 FOREIGN KEY (client_id) REFERENCES sylius_admin_api_client (id)');
        $this->addSql('ALTER TABLE sylius_admin_api_refresh_token ADD CONSTRAINT FK_9160E3FAA76ED395 FOREIGN KEY (user_id) REFERENCES sylius_admin_user (id)');
    }

    public function down(Schema $schema): void
    {
    }
}
