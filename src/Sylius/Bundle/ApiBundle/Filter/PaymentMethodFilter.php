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

namespace Sylius\Bundle\ApiBundle\Filter;

use ApiPlatform\Core\Api\FilterInterface;

final class PaymentMethodFilter implements FilterInterface
{
    public function getDescription(string $resourceClass): array
    {
        return [
            'paymentId' => [
                'type' => 'string',
                'required' => false,
                'property' => null,
                'swagger' => [
                    'name' => 'Payment',
                    'description' => 'Id of payment for which you would like to check available payment methods',
                ],
            ],
            'tokenValue' => [
                'type' => 'string',
                'required' => false,
                'property' => null,
                'swagger' => [
                    'name' => 'Order',
                    'description' => 'Token value of order for which you would like to check available payment methods. Must be set if you want to get payment methods for specific order\'s payment.',
                ],
            ],
        ];
    }
}
