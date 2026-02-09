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

namespace Sylius\Bundle\CoreBundle\ExpressionLanguage;

use Sylius\Component\Core\Context\ShopperContextInterface;
use Sylius\Resource\Symfony\ExpressionLanguage\VariablesInterface;

/**
 * @experimental
 */
final readonly class ShopperContextVariables implements VariablesInterface
{
    public function __construct(
        private ShopperContextInterface $shopperContext,
    ) {
    }

    /**
     * @return array{
     *     sylius_context_shopper: ShopperContextInterface,
     * }
     */
    public function getVariables(): array
    {
        return [
            'sylius_context_shopper' => $this->shopperContext,
        ];
    }
}
