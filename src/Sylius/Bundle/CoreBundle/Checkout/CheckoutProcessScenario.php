<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\CoreBundle\Checkout;

use Sylius\Bundle\CartBundle\Provider\CartProviderInterface;
use Sylius\Bundle\CoreBundle\Model\OrderInterface;
use Sylius\Bundle\FlowBundle\Process\Builder\ProcessBuilderInterface;
use Sylius\Bundle\FlowBundle\Process\Scenario\ProcessScenarioInterface;

/**
 * Sylius checkout process.
 *
 * @author Paweł Jędrzejewski <pjedrzejewski@diweb.pl>
 */
class CheckoutProcessScenario implements ProcessScenarioInterface
{
    /**
     * Cart provider.
     *
     * @var CartProviderInterface
     */
    protected $cartProvider;

    /**
     * Constructor.
     *
     * @param CartProviderInterface $cartProvider
     */
    public function __construct(CartProviderInterface $cartProvider)
    {
        $this->cartProvider = $cartProvider;
    }

    /**
     * {@inheritdoc}
     */
    public function build(ProcessBuilderInterface $builder)
    {
        $cart = $this->getCurrentCart();

        $builder
            ->add('security', 'sylius_checkout_security')
            ->add('addressing', 'sylius_checkout_addressing')
            ->add('shipping', 'sylius_checkout_shipping')
            ->add('payment', 'sylius_checkout_payment')
            ->add('finalize', 'sylius_checkout_finalize')
            ->add('purchase', 'sylius_checkout_purchase')
        ;

        $builder
            ->setDisplayRoute('sylius_checkout_display')
            ->setForwardRoute('sylius_checkout_forward')
            ->setRedirect('sylius_homepage')
            ->validate(function () use ($cart) {
                return !$cart->isEmpty();
            })
        ;
    }

    /**
     * Get current cart.
     *
     * @return OrderInterface
     */
    protected function getCurrentCart()
    {
        return $this->cartProvider->getCart();
    }
}
