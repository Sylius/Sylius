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

namespace Sylius\Behat\Service\Mocker;

use Mockery\MockInterface;

interface MockerInterface
{
    public function mockCollaborator(string $className): MockInterface;

    public function mockService(string $serviceId, string $className): MockInterface;

    public function unmockService(string $serviceId);

    public function unmockAll();
}
