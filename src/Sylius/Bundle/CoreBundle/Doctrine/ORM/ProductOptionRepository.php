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

namespace Sylius\Bundle\CoreBundle\Doctrine\ORM;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Mapping;
use Sylius\Bundle\ProductBundle\Doctrine\ORM\ProductOptionRepository as BaseProductOptionRepository;
use SyliusLabs\AssociationHydrator\AssociationHydrator;

class ProductOptionRepository extends BaseProductOptionRepository
{
    /**
     * @var AssociationHydrator
     */
    protected $associationHydrator;

    /**
     * {@inheritdoc}
     */
    public function __construct(EntityManager $entityManager, Mapping\ClassMetadata $class)
    {
        parent::__construct($entityManager, $class);

        $this->associationHydrator = new AssociationHydrator($entityManager, $class);
    }

    /**
     * {@inheritdoc}
     */
    public function findAll(): array
    {
        $productOptions = parent::findAll();

        $this->associationHydrator->hydrateAssociation($productOptions, 'translations');

        return $productOptions;
    }
}
