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

namespace Sylius\Behat\Context\Transform;

use Behat\Behat\Context\Context;
use Symfony\Contracts\Translation\TranslatorInterface;

final class ShippingCalculatorContext implements Context
{
    /** @var array */
    private $shippingCalculators;

    /** @var TranslatorInterface */
    private $translator;

    public function __construct(array $shippingCalculators, TranslatorInterface $translator)
    {
        $this->shippingCalculators = $shippingCalculators;
        $this->translator = $translator;
    }

    /**
     * @Transform :shippingCalculator
     */
    public function getShippingCalculatorByName(string $shippingCalculator): string
    {
        $flippedCalculators = array_flip(array_map(
            function (string $translationKey): string { return $this->translator->trans($translationKey); },
            $this->shippingCalculators
        ));

        return $flippedCalculators[$shippingCalculator];
    }
}
