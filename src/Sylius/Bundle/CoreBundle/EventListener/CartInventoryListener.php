<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\CoreBundle\EventListener;

use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Sylius\Component\Inventory\Manager\InventoryManagerInterface;
use Symfony\Bundle\FrameworkBundle\Translation\Translator;
use Symfony\Component\EventDispatcher\GenericEvent;
use Sylius\Component\Inventory\Manager\InsufficientRequirementsException;

/**
 * Order inventory processing listener.
 *
 * @author Myke Hines <myke@webhines.com>
 */
class CartInventoryListener
{
    /**
     * @var SessionInterface
     */
    protected $session;

    /**
     * @var inventoryManager
     */
    protected $inventoryManager;

    /**
     * @var translator
     */
    protected $translator;

    /**
     * Constructor.
     *
     * @param SessionInterface    $session
     */
    public function __construct(
        SessionInterface $session,
        InventoryManagerInterface $inventoryManager,
        Translator $translator
    )
    {
        $this->session = $session;
        $this->inventoryManager = $inventoryManager;
        $this->translator = $translator;
    }


	/** 
	 * Make sure our cart products meet the minimum requirements
	 * for our inventory
	 */
	public function processInventoryRestrictions(GenericEvent $event)
	{
        $order = $event->getSubject();

        $ret = true;
        foreach ($order->getItems() as $item)
        {
            try {
                $this->inventoryManager->isStockConvertable($item->getVariant(), $item->getQuantity());
            } catch (InsufficientRequirementsException $e) {
                $this->session->getBag('flashes')->add('error', $e->getMessage());    
                $ret = false;  
            }
        }
	
        return $ret;
    }    
}