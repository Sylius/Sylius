<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Behat\Service\Mocker;

use PSS\SymfonyMockerContainer\DependencyInjection\MockerContainer;

/**
 * @author Arkadiusz Krakowiak <arkadiusz.krakowiak@lakion.com>
 */
class Mocker implements MockerInterface
{
    /**
     * @var MockerContainer
     */
    private $container;

    /**
     * {@inheritdoc}
     */
    public function __construct(MockerContainer $container)
    {
        $this->container = $container;
    }

    /**
     * {@inheritdoc}
     */
    public function mockCollaborator($className)
    {
        return \Mockery::mock($className);
    }

    /**
     * {@inheritdoc}
     */
    public function mockService($serviceId, $className)
    {
        return $this->container->mock($serviceId, $className);
    }
}
