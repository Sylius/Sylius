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

namespace Sylius\Bundle\CoreBundle\Twig;

use Sylius\Bundle\CoreBundle\Templating\Helper\CheckoutStepsHelper;

final class CheckoutStepsExtension extends \Twig_Extension
{
    /**
     * @var CheckoutStepsHelper
     */
    private $checkoutStepsHelper;

    /**
     * @param CheckoutStepsHelper $checkoutStepsHelper
     */
    public function __construct(CheckoutStepsHelper $checkoutStepsHelper)
    {
        $this->checkoutStepsHelper = $checkoutStepsHelper;
    }

    /**
     * {@inheritdoc}
     */
    public function getFunctions(): array
    {
        return [
            new \Twig_Function('sylius_is_shipping_required', [$this->checkoutStepsHelper, 'isShippingRequired']),
            new \Twig_Function('sylius_is_payment_required', [$this->checkoutStepsHelper, 'isPaymentRequired']),
        ];
    }
}
