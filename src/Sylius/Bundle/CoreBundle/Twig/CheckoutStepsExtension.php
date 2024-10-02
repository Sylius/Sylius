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

use Sylius\Bundle\CoreBundle\Templating\Helper\CheckoutStepsHelper;
use Sylius\Component\Core\Checker\OrderPaymentMethodSelectionRequirementCheckerInterface;
use Sylius\Component\Core\Checker\OrderShippingMethodSelectionRequirementCheckerInterface;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

final class CheckoutStepsExtension extends AbstractExtension
{
    public function __construct(
        private readonly CheckoutStepsHelper|OrderPaymentMethodSelectionRequirementCheckerInterface $checkoutStepsHelper,
        private readonly ?OrderShippingMethodSelectionRequirementCheckerInterface $orderShippingMethodSelectionRequirementChecker = null,
    ) {
        if ($this->checkoutStepsHelper instanceof CheckoutStepsHelper) {
            trigger_deprecation(
                'sylius/core-bundle',
                '1.14',
                'Passing an instance of %s as constructor argument for %s is deprecated and will be prohibited in Sylius 2.0. Pass an instance of %s instead.',
                CheckoutStepsHelper::class,
                self::class,
                OrderPaymentMethodSelectionRequirementCheckerInterface::class,
            );

            trigger_deprecation(
                'sylius/core-bundle',
                '1.14',
                'The argument name $checkoutStepsHelper is deprecated and will be renamed to $orderPaymentMethodSelectionRequirementChecker in Sylius 2.0.',
            );
        }

        if (null === $this->orderShippingMethodSelectionRequirementChecker) {
            trigger_deprecation(
                'sylius/core-bundle',
                '1.14',
                'Not passing a $orderShippingMethodSelectionRequirementChecker to %s constructor as a second argument is deprecated and will be prohibited in Sylius 2.0. Pass an instance of %s instead.',
                self::class,
                OrderShippingMethodSelectionRequirementCheckerInterface::class,
            );
        }
    }

    public function getFunctions(): array
    {
        if (
            $this->checkoutStepsHelper instanceof OrderPaymentMethodSelectionRequirementCheckerInterface &&
            $this->orderShippingMethodSelectionRequirementChecker instanceof OrderShippingMethodSelectionRequirementCheckerInterface
        ) {
            return [
                new TwigFunction('sylius_is_shipping_required', [$this->orderShippingMethodSelectionRequirementChecker, 'isShippingMethodSelectionRequired']),
                new TwigFunction('sylius_is_payment_required', [$this->checkoutStepsHelper, 'isPaymentMethodSelectionRequired']),
            ];
        }

        return [
            new TwigFunction('sylius_is_shipping_required', [$this->checkoutStepsHelper, 'isShippingRequired']),
            new TwigFunction('sylius_is_payment_required', [$this->checkoutStepsHelper, 'isPaymentRequired']),
        ];
    }
}
