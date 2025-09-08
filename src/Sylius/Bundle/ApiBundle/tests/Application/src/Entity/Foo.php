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

namespace Sylius\Bundle\ApiBundle\Application\Entity;

use Doctrine\ORM\Mapping as ORM;
use Sylius\Component\Core\Model\AdminUserInterface;

#[ORM\Entity]
#[ORM\Table(name: 'foo')]
class Foo
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private ?int $id = null;

    #[ORM\Column(type: 'string', length: 255)]
    private ?string $name = null;

    #[ORM\ManyToOne(targetEntity: AdminUserInterface::class)]
    #[ORM\JoinColumn(name: 'owner_id')]
    private ?AdminUserInterface $owner = null;

    #[ORM\ManyToOne(targetEntity: FooSyliusResource::class)]
    #[ORM\JoinColumn(name: 'foo_sylius_resource_id')]
    private ?FooSyliusResource $fooSyliusResource = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function setId(?int $id): void
    {
        $this->id = $id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(?string $name): void
    {
        $this->name = $name;
    }

    public function getOwner(): ?AdminUserInterface
    {
        return $this->owner;
    }

    public function setOwner(?AdminUserInterface $owner): void
    {
        $this->owner = $owner;
    }

    public function getFooSyliusResource(): ?FooSyliusResource
    {
        return $this->fooSyliusResource;
    }

    public function setFooSyliusResource(?FooSyliusResource $fooSyliusResource): void
    {
        $this->fooSyliusResource = $fooSyliusResource;
    }
}
