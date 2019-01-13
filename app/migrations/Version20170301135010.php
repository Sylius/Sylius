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

class Version20170301135010 extends AbstractMigration
{
    public function up(Schema $schema): void
    {
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE sylius_product_image_product_variants (image_id INT NOT NULL, variant_id INT NOT NULL, INDEX IDX_8FFDAE8D3DA5256D (image_id), INDEX IDX_8FFDAE8D3B69A9AF (variant_id), PRIMARY KEY(image_id, variant_id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE sylius_product_image_product_variants ADD CONSTRAINT FK_8FFDAE8D3DA5256D FOREIGN KEY (image_id) REFERENCES sylius_product_image (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE sylius_product_image_product_variants ADD CONSTRAINT FK_8FFDAE8D3B69A9AF FOREIGN KEY (variant_id) REFERENCES sylius_product_variant (id) ON DELETE CASCADE');
    }

    public function down(Schema $schema): void
    {
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('DROP TABLE sylius_product_image_product_variants');
    }
}
