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
class PayOrderPage extends SymfonyPage implements PayOrderPageInterface
{
    /**
     * {@inheritdoc}
     */
    public function getRouteName()
    {
        return 'sylius_account_order_payment_index';
    }

    /**
     * {@inheritdoc}
     */
    public function clickPayButtonForGivenPayment(PaymentInterface $payment)
    {
        $this->getDocument()->clickLink(sprintf('delete_%s', $payment->getId()));
    }

    /**
     * {@inheritdoc}
     */
    public function countCancelledPayments()
    {
        return $this->countPaymentsWithSpecificState(PaymentInterface::STATE_CANCELLED);
    }

    /**
     * {@inheritdoc}
     */
    public function countNewPayments()
    {
        return $this->countPaymentsWithSpecificState(PaymentInterface::STATE_NEW);
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
            throw new \RuntimeException(sprintf('%s getUrl method require order number', self::class));
        }

        return parent::getUrl($urlParameters);
    }

    /**
     * @param string $state
     *
     * @return int
     */
    private function countPaymentsWithSpecificState($state)
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
}
