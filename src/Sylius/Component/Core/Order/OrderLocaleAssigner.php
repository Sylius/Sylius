<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Component\Core\Order;

use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Locale\Context\LocaleContextInterface;

/**
 * @author Kamil Kokot <kamil.kokot@lakion.com>
 */
final class OrderLocaleAssigner implements OrderLocaleAssignerInterface
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
     * {@inheritdoc}
     */
    public function assignLocale(OrderInterface $order)
    {
        $order->setLocaleCode($this->localeContext->getLocaleCode());
    }
}
