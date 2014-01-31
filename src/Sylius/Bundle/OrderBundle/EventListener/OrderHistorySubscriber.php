<?php
/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\OrderBundle\EventListener;

use Doctrine\Common\EventSubscriber;
use Doctrine\ORM\Event\PreUpdateEventArgs;
use Doctrine\ORM\Event\LifecycleEventArgs;
use Sylius\Bundle\OrderBundle\Model\OrderInterface;
use Sylius\Bundle\OrderBundle\Model\History;

class OrderHistorySubscriber implements EventSubscriber
{
    private $history = null;

    public function getSubscribedEvents()
    {
        return array(
            'preUpdate',
            'postUpdate'
        );
    }

    /**
     * This is a lifecycle event to see if our order history
     * is being changed. If so, lets log it in our history entity
     */
    public function preUpdate(PreUpdateEventArgs $args)
    {
        $entity = $args->getEntity();

        if ($entity instanceof OrderInterface && ( $args->hasChangedField('state'))) {
            $history = new History();
            $history->setOrder($entity);
            $history->getState($args->getNewValue('state'));
            $history->setNotifyCustomer(false);

            $this->history = $history;
        }
    }

    /**
     * Flush our history entity if it was created during the
     * preUpdate event
     */
    public function postUpdate(LifecycleEventArgs $args)
    {
        if ($this->hasHistory()) {
            $em = $args->getObjectManager();
            $em->persist($this->history);
            $em->flush();
        }
    }

    /**
     * Checks to see if history property was set
     */
    public function hasHistory()
    {
        return is_null($this->history) ? false : true;
    }
}
