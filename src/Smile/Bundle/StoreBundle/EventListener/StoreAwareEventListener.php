<?php

namespace Smile\Bundle\StoreBundle\EventListener;


use Doctrine\Common\EventSubscriber;
use Doctrine\ORM\Event\LifecycleEventArgs;
use Doctrine\ORM\Events;
use Sylius\Component\Scope\ScopeAwareInterface;
use Smile\Component\Store\Context\StoreContextInterface;

class StoreAwareEventListener implements EventSubscriber
{
    /**
     * @var StoreContextInterface
     */
    private $storeContext;

    public function __construct(StoreContextInterface $storeContext)
    {
        $this->storeContext = $storeContext;
    }

    /**
     * {@inheritdoc}
     */
    public function getSubscribedEvents()
    {
        return array(
            Events::postLoad,
        );
    }

    public function postLoad(LifecycleEventArgs $args)
    {
        $entity = $args->getObject();

        if ($entity instanceof ScopeAwareInterface) {
            $entity->setCurrentScope($this->storeContext->getStore());
        }
    }
}