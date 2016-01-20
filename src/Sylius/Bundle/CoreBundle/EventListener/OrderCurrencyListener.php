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

use Sylius\Bundle\ResourceBundle\Doctrine\ORM\EntityRepository;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Currency\Context\CurrencyContextInterface;
use Sylius\Component\Resource\Exception\UnexpectedTypeException;
use Symfony\Component\EventDispatcher\GenericEvent;

/**
 * Sets currently selected currency on order object.
 *
 * @author Saša Stamenković <umpirsky@gmail.com>
 * @author Fernando Caraballo Ortiz <caraballo.ortiz@gmail.com>
 */
class OrderCurrencyListener
{
    /**
     * @var CurrencyContextInterface
     */
    protected $currencyContext;

    /**
     * @var EntityRepository
     */
    protected $currencyRepository;

    /**
     * @param CurrencyContextInterface $currencyContext
     * @param EntityRepository         $currencyRepository
     */
    public function __construct(CurrencyContextInterface $currencyContext, EntityRepository $currencyRepository)
    {
        $this->currencyContext    = $currencyContext;
        $this->currencyRepository = $currencyRepository;
    }

    /**
     * Sets currency on the order
     *
     * @throws UnexpectedTypeException when event's subject is not an order
     *
     * @param GenericEvent $event
     */
    public function processOrderCurrency(GenericEvent $event)
    {
        $order = $event->getSubject();

        if (!$order instanceof OrderInterface) {
            throw new UnexpectedTypeException(
                $order,
                OrderInterface::class
            );
        }

        $currency = $this->currencyRepository->findOneBy(['code' => $this->currencyContext->getCurrency()]);

        if ($this->currencyContext->getDefaultCurrency() !== $exchangeRate = $currency->getCode()) {
            $order->setExchangeRate($currency->getExchangeRate());
        }

        $order->setCurrency($this->currencyContext->getCurrency());
    }
}
