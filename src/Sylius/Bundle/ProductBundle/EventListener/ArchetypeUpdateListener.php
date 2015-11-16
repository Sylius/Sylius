<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\ProductBundle\EventListener;

use Sylius\Component\Archetype\Builder\ArchetypeBuilderInterface;
use Sylius\Component\Archetype\Model\ArchetypeInterface;
use Sylius\Component\Resource\Event\ResourceEvent;
use Sylius\Component\Resource\Exception\UnexpectedTypeException;
use Sylius\Component\Resource\Manager\ResourceManagerInterface;
use Sylius\Component\Resource\Repository\ResourceRepositoryInterface;

/**
 * Product update listener.
 *
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 */
class ArchetypeUpdateListener
{
    /**
     * @var ArchetypeBuilderInterface
     */
    protected $builder;

    /**
     * @var ResourceRepositoryInterface
     */
    protected $productRepository;

    /**
     * @var ResourceManagerInterface
     */
    protected $productManager;

    /**
     * @param ArchetypeBuilderInterface $builder
     * @param ResourceRepositoryInterface $productRepository
     * @param ResourceManagerInterface $productManager
     */
    public function __construct(ArchetypeBuilderInterface $builder, ResourceRepositoryInterface $productRepository, ResourceManagerInterface $productManager)
    {
        $this->builder = $builder;
        $this->productRepository = $productRepository;
        $this->productManager = $productManager;
    }

    /**
     * @param ResourceEvent $event
     */
    public function onArchetypeUpdate(ResourceEvent $event)
    {
        $archetype = $event->getResource();

        if (!$archetype instanceof ArchetypeInterface) {
            throw new UnexpectedTypeException($archetype, 'Sylius\Component\Archetype\Model\ArchetypeInterface');
        }

        $products = $this->productRepository->findBy(array('archetype' => $archetype));

        foreach ($products as $product) {
            $this->builder->build($product);

            $this->productManager->persist($product);
        }

        $this->productManager->flush();
    }
}
