<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Behat\Context\Setup;

use Behat\Behat\Context\Context;
use Doctrine\Common\Persistence\ObjectManager;
use Sylius\Bundle\CoreBundle\Fixture\Factory\ExampleFactoryInterface;
use Sylius\Behat\Service\SharedStorageInterface;
use Sylius\Component\Resource\Repository\RepositoryInterface;

/**
 * @author Kamil Kokot <kamil.kokot@lakion.com>
 */
final class RouteContext implements Context
{
    /**
     * @var SharedStorageInterface
     */
    private $sharedStorage;

    /**
     * @var ExampleFactoryInterface
     */
    private $routeExampleFactory;

    /**
     * @var ObjectManager
     */
    private $routeManager;

    /**
     * @var RepositoryInterface
     */
    private $staticContentRepository;

    /**
     * @param SharedStorageInterface $sharedStorage
     * @param ExampleFactoryInterface $routeExampleFactory
     * @param ObjectManager $routeManager
     * @param RepositoryInterface $staticContentRepository
     */
    public function __construct(
        SharedStorageInterface $sharedStorage,
        ExampleFactoryInterface $routeExampleFactory,
        ObjectManager $routeManager,
        RepositoryInterface $staticContentRepository
    ) {
        $this->sharedStorage = $sharedStorage;
        $this->routeExampleFactory = $routeExampleFactory;
        $this->routeManager = $routeManager;
        $this->staticContentRepository = $staticContentRepository;
    }

    /**
     * @Given the store has route :name
     */
    public function theStoreHasRoute($name)
    {
        $route = $this->routeExampleFactory->create(['name' => $name]);

        $this->routeManager->persist($route);
        $this->routeManager->flush();

        $this->sharedStorage->set('route', $route);
    }

    /**
     * @Given the store has routes :firstName and :secondName
     */
    public function theStoreHasRoutes(...$routesNames)
    {
        foreach ($routesNames as $routesName) {
            $this->theStoreHasRoute($routesName);
        }
    }

    /**
     * @Given the store has route :name with :contentTitle as its content
     */
    public function theStoreHasRouteWithAsItsContent($name, $contentTitle)
    {
        $content = $this->staticContentRepository->findOneBy(['title' => $contentTitle]);

        $route = $this->routeExampleFactory->create(['name' => $name, 'content' => $content]);

        $this->routeManager->persist($route);
        $this->routeManager->flush();

        $this->sharedStorage->set('route', $route);
    }
}
