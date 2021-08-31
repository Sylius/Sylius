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

namespace Sylius\Bundle\ApiBundle\Application\Entity;

use Sylius\Component\Core\Model\AdminUserInterface;

class Foo
{
    private ?int $id = null;

    private ?string $name = null;

    private ?AdminUserInterface $owner = null;

    private ?FooSyliusResource $fooSyliusResource = null;

    public function getId(): int
    {
        return $this->id;
    }

    public function setId(int $id): void
    {
        $this->id = $id;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): void
    {
        $this->name = $name;
    }

    public function getOwner(): AdminUserInterface
    {
        return $this->owner;
    }

    public function setOwner(AdminUserInterface $owner): void
    {
        $this->owner = $owner;
    }

    public function getFooSyliusResource(): FooSyliusResource
    {
        return $this->fooSyliusResource;
    }

    public function setFooSyliusResource(FooSyliusResource $fooSyliusResource): void
    {
        $this->fooSyliusResource = $fooSyliusResource;
    }
}
