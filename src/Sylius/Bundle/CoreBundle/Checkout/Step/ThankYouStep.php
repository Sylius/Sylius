<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\CoreBundle\Checkout\Step;

use Sylius\Bundle\CoreBundle\Event\OrderCompleteEvent;
use Sylius\Bundle\FlowBundle\Process\Context\ProcessContextInterface;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\SyliusCheckoutEvents;

/**
 * Thank you checkout step.
 *
 * @author Antonio Peric <antonio@locastic.com>
 */
class ThankYouStep extends CheckoutStep
{
    /**
     * {@inheritdoc}
     */
    public function displayAction(ProcessContextInterface $context)
    {
        $order = $this->getCurrentCart();
        $this->dispatchCheckoutEvent(SyliusCheckoutEvents::THANK_YOU_INITIALIZE, $order);

        $this->dispatchCheckoutEvent(SyliusCheckoutEvents::THANK_YOU_PRE_COMPLETE, $order);

        $context->close();

        $event = new OrderCompleteEvent($order);
        $this->dispatchEvent(SyliusCheckoutEvents::PURCHASE_COMPLETE, $event);

        if ($event->hasResponse()) {
            return $event->getResponse();
        }

        return $this->renderStep($order);
    }

    protected function renderStep(OrderInterface $order)
    {
        return $this->render(
            $this->container->getParameter(sprintf('sylius.checkout.step.%s.template', $this->getName())),
            array(
                'order' => $order
            )
        );
    }
}
