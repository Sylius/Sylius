<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Behat;

/**
 * @author Arkadiusz Krakowiak <arkadiusz.krakowiak@lakion.com>
 */
interface BehatMockerInterface
{
    /**
     * @param string $className
     *
     * @return \Mockery\MockInterface
     */
    public function mockCollaborator($className);

    /**
     * @param string $serviceId
     * @param string $className
     *
     * @return \Mockery\MockInterface
     */
    public function mockService($serviceId, $className);
}
