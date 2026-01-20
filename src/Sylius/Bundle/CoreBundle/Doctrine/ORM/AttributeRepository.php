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

namespace Sylius\Bundle\CoreBundle\Doctrine\ORM;

use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Mapping\ClassMetadata;
use Sylius\Bundle\AttributeBundle\Doctrine\ORM\AttributeRepository as BaseAttributeRepository;
use Sylius\Component\Attribute\Model\AttributeInterface;
use SyliusLabs\AssociationHydrator\AssociationHydrator;

/**
 * @template T of AttributeInterface
 */
class AttributeRepository extends BaseAttributeRepository
{
    protected AssociationHydrator $associationHydrator;

    public function __construct(EntityManagerInterface $entityManager, ClassMetadata $class)
    {
        parent::__construct($entityManager, $class);

        $this->associationHydrator = new AssociationHydrator($entityManager, $class);
    }

    public function findAll(): array
    {
        $attributes = parent::findAll();

        $this->associationHydrator->hydrateAssociation($attributes, 'translations');

        return $attributes;
    }
}
