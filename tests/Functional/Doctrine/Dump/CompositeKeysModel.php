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

use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: 'composite_keys_model')]
class CompositeKeysModel
{
    public function __construct(
        #[ORM\Id]
        #[ORM\Column(length: 250)]
        public string $email,

        #[ORM\Id]
        #[ORM\Column(name: "organization_name", type: "string")]
        public string $organizationName,

        #[ORM\Column(name: "description", type: 'string')]
        public string $description,
    ) {
    }
}
