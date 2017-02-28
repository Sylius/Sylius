<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\CoreBundle\Doctrine\ORM;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Mapping;
use Sylius\Bundle\ResourceBundle\Doctrine\ORM\EntityRepository;

/**
 * @author Kamil Kokot <kamil.kokot@lakion.com>
 */
class AttributeRepository extends EntityRepository
{
    /**
     * @var AssociationHydrator
     */
    protected $associationHydrator;

    /**
     * {@inheritdoc}
     */
    public function __construct(EntityManager $em, Mapping\ClassMetadata $class)
    {
        parent::__construct($em, $class);

        $this->associationHydrator = new AssociationHydrator($this->_em, $class);
    }

    /**
     * {@inheritdoc}
     */
    public function findAll()
    {
        $attributes = parent::findAll();

        $this->associationHydrator->hydrateAssociation($attributes, 'translations');

        return $attributes;
    }
}
