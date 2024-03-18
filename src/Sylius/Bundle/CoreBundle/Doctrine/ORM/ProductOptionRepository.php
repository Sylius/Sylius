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

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Mapping\ClassMetadata;
use Sylius\Bundle\ProductBundle\Doctrine\ORM\ProductOptionRepository as BaseProductOptionRepository;
use Sylius\Component\Product\Model\ProductOptionInterface;
use SyliusLabs\AssociationHydrator\AssociationHydrator;

/**
 * @template T of ProductOptionInterface
 *
 * @extends BaseProductOptionRepository<T>
 */
class ProductOptionRepository extends BaseProductOptionRepository
{
    protected AssociationHydrator $associationHydrator;

    public function __construct(EntityManager $entityManager, ClassMetadata $class)
    {
        parent::__construct($entityManager, $class);

        $this->associationHydrator = new AssociationHydrator($entityManager, $class);
    }

    public function findAll(): array
    {
        $productOptions = parent::findAll();

        $this->associationHydrator->hydrateAssociation($productOptions, 'translations');

        return $productOptions;
    }
}
