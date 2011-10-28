<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\SalesBundle\Model;

use Symfony\Component\Translation\TranslatorInterface;

class StatusManager
{
    protected $statuses;
    protected $translator;
    
    public function __construct(array $statuses, TranslatorInterface $translator = null)
    {
        $this->statuses = $statuses;
        $this->translator = $translator;
    }
    
    public function resolveStatus(OrderInterface $order)
    {
        $this->translateStatus($order->getStatus());
    }
    
    public function translateStatus($status)
    {
        if (isset($this->statuses[$status])) {
            if (null != $this->translator) {
                return $this->translator->trans($this->statuses[$status], array(), 'Statuses');
            }
            
            return $this->statuses[$status];
        }
        
        throw new \InvalidArgumentException('Wrong status alias supplied.');
    }
    
    public function getStatuses()
    {
        return $this->statuses;
    }
}
