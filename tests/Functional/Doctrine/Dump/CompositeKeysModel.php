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

namespace Sylius\Tests\Functional\Doctrine\Dump;

use Doctrine\ORM\Mapping\Column;
use Doctrine\ORM\Mapping\Entity;
use Doctrine\ORM\Mapping\Id;
use Doctrine\ORM\Mapping\Table;

/**
 * @Entity
 *
 * @Table(name="composite_keys_model")
 */
class CompositeKeysModel
{
    public function __construct(
        /**
         * @Column(length=250)
         *
         * @Id
         */
        public string $email,

        /**
         * @Column(type="string", name="organization_name")
         *
         * @Id
         */
        public string $organizationName,

        /**
         * @Column(type="string", name="description")
         */
        public string $description,
    ) {
    }
}
