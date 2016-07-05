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
use Sylius\Component\Core\Test\Services\SharedStorageInterface;

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
     * @param SharedStorageInterface $sharedStorage
     * @param ExampleFactoryInterface $routeExampleFactory
     * @param ObjectManager $routeManager
     */
    public function __construct(
        SharedStorageInterface $sharedStorage,
        ExampleFactoryInterface $routeExampleFactory,
        ObjectManager $routeManager
    ) {
        $this->sharedStorage = $sharedStorage;
        $this->routeExampleFactory = $routeExampleFactory;
        $this->routeManager = $routeManager;
    }
}
