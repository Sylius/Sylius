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
use Sylius\Bundle\ResourceBundle\Doctrine\ORM\EntityRepository;
use SyliusLabs\AssociationHydrator\AssociationHydrator;

class AttributeRepository extends EntityRepository
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
        $attributes = parent::findAll();

        $this->associationHydrator->hydrateAssociation($attributes, 'translations');

        return $attributes;
    }
}
