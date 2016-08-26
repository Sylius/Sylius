<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\ContentBundle\Factory;

use Doctrine\Common\Persistence\ObjectManager;
use Sylius\Bundle\ContentBundle\Document\Route;
use Sylius\Component\Resource\Factory\FactoryInterface;

/**
 * @author Kamil Kokot <kamil.kokot@lakion.com>
 */
final class RouteFactory implements FactoryInterface
{
    /**
     * @var FactoryInterface
     */
    private $decoratedFactory;

    /**
     * @var ObjectManager
     */
    private $documentManager;

    /**
     * @var string
     */
    private $routeParentPath;

    /**
     * @param FactoryInterface $decoratedFactory
     * @param ObjectManager $documentManager
     * @param string $routeParentPath
     */
    public function __construct(FactoryInterface $decoratedFactory, ObjectManager $documentManager, $routeParentPath)
    {
        $this->decoratedFactory = $decoratedFactory;
        $this->documentManager = $documentManager;
        $this->routeParentPath = $routeParentPath;
    }

    /**
     * {@inheritdoc}
     */
    public function createNew()
    {
        /** @var Route $route */
        $route = $this->decoratedFactory->createNew();
        $route->setParentDocument($this->documentManager->find(null, $this->routeParentPath));

        return $route;
    }
}
