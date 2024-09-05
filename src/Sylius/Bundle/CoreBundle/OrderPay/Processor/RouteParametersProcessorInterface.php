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

namespace Sylius\Bundle\CoreBundle\OrderPay\Processor;

interface RouteParametersProcessorInterface
{
    /**
     * @param array<string, string|int|bool> $rawParameters
     * @param array<string, mixed> $context
     */
    public function process(string $route, array $rawParameters = [], array $context = []): string;
}
