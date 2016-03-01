<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Behat\Page\Order;

use Sylius\Behat\Page\SymfonyPage;
use Sylius\Component\Core\Model\PaymentInterface;

/**
 * @author Arkadiusz Krakowiak <arkadiusz.krakowiak@lakion.com>
 */
class OrderPaymentsPage extends SymfonyPage implements OrderPaymentsPageInterface
{
    /**
     * {@inheritdoc}
     */
    public function getRouteName()
    {
        return 'sylius_order_payment_index';
    }

    /**
     * {@inheritdoc}
     */
    public function clickPayButtonForGivenPayment(PaymentInterface $payment)
    {
        $this->getDocument()->clickLink(sprintf('pay_%s', $payment->getId()));
    }

    /**
     * {@inheritdoc}
     */
    public function countPaymentWithSpecificState($state)
    {
        $elements = $this->getDocument()->findAll('css', '#payments > tr');

        $counter = 0;
        foreach ($elements as $element) {
            $text = $element->getText();

            if (false !== strpos($text, $state)) {
                $counter++;
            }
        }

        return $counter;
    }

    /**
     * {@inheritdoc}
     */
    public function waitForResponse($timeout, array $parameters)
    {
        $this->getDocument()->waitFor($timeout, function () use ($parameters) {
            return $this->isOpen($parameters);
        });
    }

    /**
     * {@inheritdoc}
     */
    protected function getUrl(array $urlParameters = [])
    {
        if (!isset($urlParameters['number'])) {
            throw new \InvalidArgumentException(sprintf('This page %s requires order number to be passed as parameter', self::class));
        }

        return parent::getUrl($urlParameters);
    }
}
