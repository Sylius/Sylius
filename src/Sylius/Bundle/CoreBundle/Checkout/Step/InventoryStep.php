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

use Sylius\Bundle\FlowBundle\Process\Context\ProcessContextInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Sylius\Component\Core\SyliusCheckoutEvents;
use Sylius\Component\Inventory\Manager\InsufficientRequirementsException;

/**
 * Inventory step.
 *
 * Checks to make sure all inventory is available
 *
 * @author Myke Hines <myke@webhines.com>
 */
class InventoryStep extends CheckoutStep
{
    /**
     * {@inheritdoc}
     */
    public function displayAction(ProcessContextInterface $context)
    {
        $order = $this->getCurrentCart();

        $error = false;
        foreach ($order->getItems() as $item)
        {
            try {
                $this->get('sylius.manager.inventory')->isStockConvertable($item->getVariant(), $item->getQuantity());
            } catch (InsufficientRequirementsException $e) {
                $this->getSession()->getBag('flashes')->add('error', $e->getMessage());    
                $error = true;                
            }
        }

        if (!$error)
            return $this->complete();
        else
            return new RedirectResponse($this->getRouter()->generate('sylius_cart_summary'));
    }

    /**
     * {@inheritdoc}
     */
    public function forwardAction(ProcessContextInterface $context)
    {
        $order = $this->getCurrentCart();        
        $this->dispatchCheckoutEvent(SyliusCheckoutEvents::INVENTORY_OMPLETE, $order);

        return $this->complete();
    }

    /**
     * {@inheritdoc}
     */
    public function getRouter()
    {
        return $this->get('router');
    }

    /**
     * {@inheritdoc}
     */
    public function getSession()
    {
        return $this->get('session');
    }

}
