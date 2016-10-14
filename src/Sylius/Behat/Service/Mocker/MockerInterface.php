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

use Mockery\MockInterface;

/**
 * @author Arkadiusz Krakowiak <arkadiusz.krakowiak@lakion.com>
 */
interface MockerInterface
{
    /**
     * @param string $className
     *
     * @return MockInterface
     */
    public function mockCollaborator($className);

    /**
     * @param string $serviceId
     * @param string $className
     *
     * @return MockInterface
     */
    public function mockService($serviceId, $className);

    /**
     * @param string $serviceId
     */
    public function unmockService($serviceId);

    public function unmockAll();
}
