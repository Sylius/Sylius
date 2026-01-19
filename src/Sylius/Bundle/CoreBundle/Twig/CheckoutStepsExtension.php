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

namespace Sylius\Bundle\CoreBundle\Twig;

use Sylius\Component\Core\Checker\OrderPaymentMethodSelectionRequirementCheckerInterface;
use Sylius\Component\Core\Checker\OrderShippingMethodSelectionRequirementCheckerInterface;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

final class CheckoutStepsExtension extends AbstractExtension
{
    public function __construct(
        private readonly OrderPaymentMethodSelectionRequirementCheckerInterface $orderPaymentMethodSelectionRequirementChecker,
        private readonly OrderShippingMethodSelectionRequirementCheckerInterface $orderShippingMethodSelectionRequirementChecker,
    ) {
    }

    public function getFunctions(): array
    {
        return [
            new TwigFunction('sylius_is_shipping_required', [$this->orderShippingMethodSelectionRequirementChecker, 'isShippingMethodSelectionRequired']),
            new TwigFunction('sylius_is_payment_required', [$this->orderPaymentMethodSelectionRequirementChecker, 'isPaymentMethodSelectionRequired']),
        ];
    }
}
