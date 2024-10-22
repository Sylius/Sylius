<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Sylius Sp. z o.o.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Sylius\Bundle\CoreBundle\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Sylius\Bundle\CoreBundle\Doctrine\Migrations\AbstractMigration;

class Version20170214095710 extends AbstractMigration
{
    public function up(Schema $schema): void
    {
        $this->addSql('ALTER TABLE sylius_product_review DROP FOREIGN KEY FK_C7056A99F675F31B');
        $this->addSql('ALTER TABLE sylius_product_review CHANGE author_id author_id INT NOT NULL');
        $this->addSql('ALTER TABLE sylius_product_review ADD CONSTRAINT FK_C7056A99F675F31B FOREIGN KEY (author_id) REFERENCES sylius_customer (id) ON DELETE CASCADE');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE sylius_product_review DROP FOREIGN KEY FK_C7056A99F675F31B');
        $this->addSql('ALTER TABLE sylius_product_review CHANGE author_id author_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE sylius_product_review ADD CONSTRAINT FK_C7056A99F675F31B FOREIGN KEY (author_id) REFERENCES sylius_customer (id)');
    }
}
