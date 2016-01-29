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

use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\Common\Persistence\ObjectRepository;
use Sylius\Component\Archetype\Builder\ArchetypeBuilderInterface;
use Sylius\Component\Archetype\Model\ArchetypeInterface;
use Sylius\Component\Resource\Exception\UnexpectedTypeException;
use Symfony\Component\EventDispatcher\GenericEvent;

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
     * @var ObjectRepository
     */
    protected $productRepository;

    /**
     * @var ObjectManager
     */
    protected $productManager;

    /**
     * @param ArchetypeBuilderInterface $builder
     * @param ObjectRepository $productRepository
     * @param ObjectManager $productManager
     */
    public function __construct(ArchetypeBuilderInterface $builder, ObjectRepository $productRepository, ObjectManager $productManager)
    {
        $this->builder = $builder;
        $this->productRepository = $productRepository;
        $this->productManager = $productManager;
    }

    /**
     * @param GenericEvent $event
     */
    public function onArchetypeUpdate(GenericEvent $event)
    {
        $archetype = $event->getSubject();

        if (!$archetype instanceof ArchetypeInterface) {
            throw new UnexpectedTypeException($archetype, ArchetypeInterface::class);
        }

        $products = $this->productRepository->findBy(['archetype' => $archetype]);

        foreach ($products as $product) {
            $this->builder->build($product);

            $this->productManager->persist($product);
        }
    }
}
