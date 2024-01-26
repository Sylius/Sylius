<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Sylius Sp. z o.o.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Sylius\Bundle\PayumBundle\Tests\Stub;

use Sylius\Bundle\PayumBundle\Attribute\AsGatewayConfigurationType;

#[AsGatewayConfigurationType(type: 'test', label: 'Test', priority: 15)]
final class GatewayConfigurationTypeStub
{
}
