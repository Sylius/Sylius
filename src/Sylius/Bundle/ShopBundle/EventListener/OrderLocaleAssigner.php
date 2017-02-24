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

use Sylius\Bundle\ResourceBundle\Event\ResourceControllerEvent;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Locale\Context\LocaleContextInterface;
use Webmozart\Assert\Assert;

/**
 * @author Kamil Kokot <kamil.kokot@lakion.com>
 */
final class OrderLocaleAssigner
{
    /**
     * @var LocaleContextInterface
     */
    private $localeContext;

    /**
     * @param LocaleContextInterface $localeContext
     */
    public function __construct(LocaleContextInterface $localeContext)
    {
        $this->localeContext = $localeContext;
    }

    /**
     * @param ResourceControllerEvent $event
     */
    public function assignLocale(ResourceControllerEvent $event)
    {
        /** @var OrderInterface $order */
        $order = $event->getSubject();

        Assert::isInstanceOf($order, OrderInterface::class);

        $order->setLocaleCode($this->localeContext->getLocaleCode());
    }
}
