<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Behat\Page\Shop\Checkout;

use Sylius\Behat\Page\SymfonyPage;

/**
 * @author Grzegorz Sadowski <grzegorz.sadowski@lakion.com>
 */
class CanceledPaymentPage extends SymfonyPage implements CanceledPaymentPageInterface
{
    /**
     * @return string
     */
    public function getRouteName()
    {
        return 'sylius_shop_checkout_canceled_payment';
    }

    /**
     * {@inheritdoc}
     */
    public function clickPayButton()
    {
        $payButton = $this->getElement('pay_button');
        $payButton->clickLink('Pay');
    }

    /**
     * {@inheritdoc}
     */
    public function waitForResponse($timeout, array $parameters = [])
    {
        $this->getDocument()->waitFor($timeout, function () use ($parameters) {
            return $this->isOpen($parameters);
        });
    }

    /**
     * {@inheritdoc}
     */
    protected function getDefinedElements()
    {
        return array_merge(parent::getDefinedElements(), [
            'pay_button' => '#pay',
        ]);
    }
}
