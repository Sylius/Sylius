<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\ShopBundle\EventListener;

use Sylius\Bundle\CoreBundle\Assigner\IpAssignerInterface;
use Sylius\Component\Core\Model\OrderInterface;
use Symfony\Component\EventDispatcher\GenericEvent;
use Symfony\Component\HttpFoundation\RequestStack;
use Webmozart\Assert\Assert;

/**
 * @author Mateusz Zalewski <mateusz.zalewski@lakion.com>
 */
final class OrderCustomerIpListener
{
    /**
     * @var IpAssignerInterface
     */
    private $ipAssigner;

    /**
     * @var RequestStack
     */
    private $requestStack;

    /**
     * @param IpAssignerInterface $ipAssigner
     * @param RequestStack $requestStack
     */
    public function __construct(IpAssignerInterface $ipAssigner, RequestStack $requestStack)
    {
        $this->ipAssigner = $ipAssigner;
        $this->requestStack = $requestStack;
    }

    /**
     * @param GenericEvent $event
     */
    public function assignCustomerIpToOrder(GenericEvent $event)
    {
        $subject = $event->getSubject();
        Assert::isInstanceOf($subject, OrderInterface::class);

        $this->ipAssigner->assign($subject, $this->requestStack->getMasterRequest());
    }
}
