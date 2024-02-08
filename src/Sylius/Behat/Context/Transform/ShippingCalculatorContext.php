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

namespace Sylius\Behat\Context\Transform;

use Behat\Behat\Context\Context;
use Symfony\Contracts\Translation\TranslatorInterface;

final class ShippingCalculatorContext implements Context
{
    public function __construct(
        private array $shippingCalculators,
        private TranslatorInterface $translator,
    ) {
    }

    /**
     * @Transform :shippingCalculator
     */
    public function getShippingCalculatorByName(string $shippingCalculator): string
    {
        $flippedCalculators = array_flip(array_map(
            fn (string $translationKey): string => $this->translator->trans($translationKey),
            $this->shippingCalculators,
        ));

        return $flippedCalculators[$shippingCalculator];
    }
}
