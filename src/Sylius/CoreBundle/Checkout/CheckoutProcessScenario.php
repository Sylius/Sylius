<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\CoreBundle\Checkout;

use Sylius\FlowBundle\Process\Builder\ProcessBuilderInterface;
use Sylius\FlowBundle\Process\Scenario\ProcessScenarioInterface;
use Sylius\Cart\Provider\CartProviderInterface;
use Sylius\Core\Model\OrderInterface;

/**
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 */
class CheckoutProcessScenario implements ProcessScenarioInterface
{
    /**
     * @var CartProviderInterface
     */
    protected $cartProvider;

    /**
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
        $builder
            ->add('security', 'sylius_checkout_security')
            ->add('addressing', 'sylius_checkout_addressing')
            ->add('shipping', 'sylius_checkout_shipping')
            ->add('payment', 'sylius_checkout_payment')
            ->add('finalize', 'sylius_checkout_finalize')
            ->setDisplayRoute('sylius_checkout_display')
            ->setForwardRoute('sylius_checkout_forward')
            ->setRedirect('sylius_order_purchase')
            ->setRedirectParams([
                'paymentId' => $this->getLastNewPaymentId(),
            ])
            ->validate(function () {
                return !$this->getCurrentCart()->isEmpty();
            })
        ;
    }

    /**
     * @return OrderInterface
     */
    protected function getCurrentCart()
    {
        return $this->cartProvider->getCart();
    }

    /**
     * @return mixed
     */
    protected function getLastNewPaymentId()
    {
        $lastNewPayment = $this->getCurrentCart()->getLastPayment();

        if (null !== $lastNewPayment) {
            return $lastNewPayment->getId();
        }

        return null;
    }
}
