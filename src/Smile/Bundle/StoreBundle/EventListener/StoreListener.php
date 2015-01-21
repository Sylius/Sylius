<?php

namespace Smile\Bundle\StoreBundle\EventListener;


use Sylius\Component\Store\Context\StoreContextInterface;
use Sylius\Component\Resource\Repository\RepositoryInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;
use Symfony\Component\HttpKernel\KernelEvents;

class StoreListener implements EventSubscriberInterface
{
    /**
     * @var StoreContextInterface
     */
    protected $storeContext;

    /**
     * @var RepositoryInterface
     */
    protected $repository;

    public function __construct(StoreContextInterface $storeContext, RepositoryInterface $repository)
    {
        $this->storeContext = $storeContext;
        $this->repository = $repository;
    }

    public function onKernelRequest(GetResponseEvent $event)
    {
        $host = $event->getRequest()->getHttpHost();

        $store = $this->repository->findOneBy(
            array('url' => $host)
        );

        $this->storeContext->setStore($store);
    }

    public static function getSubscribedEvents()
    {
        return array(
            KernelEvents::REQUEST => array(array('onKernelRequest', 100)),
        );
    }
}