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

use Sylius\Component\Order\Context\CartContextInterface;
use Sylius\Resource\Symfony\ExpressionLanguage\VariablesInterface;

final readonly class CartContextVariables implements VariablesInterface
{
    public function __construct(
        private CartContextInterface $cartContext,
    ) {
    }

    /**
     * @return array{
     *     sylius_context_cart: CartContextInterface,
     * }
     */
    public function getVariables(): array
    {
        return [
            'sylius_context_cart' => $this->cartContext,
        ];
    }
}
