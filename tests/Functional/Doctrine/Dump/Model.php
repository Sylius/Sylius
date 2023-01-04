<?php

declare(strict_types=1);

namespace Sylius\Tests\Functional\Doctrine\Dump;

use Doctrine\ORM\Mapping\Column;
use Doctrine\ORM\Mapping\Entity;
use Doctrine\ORM\Mapping\GeneratedValue;
use Doctrine\ORM\Mapping\Id;
use Doctrine\ORM\Mapping\Table;

/**
 * @Entity
 * @Table(name="model")
 */
class Model
{
    public function __construct(
        /**
         * @Column(type="integer")
         * @Id
         * @GeneratedValue
         */
        public int $id,

        /**
         * @Column(length=250)
         */
        public string $email
    ) {
    }
}
