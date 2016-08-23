<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Component\Core\Updater;

use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Currency\Context\CurrencyContextInterface;
use Sylius\Component\Currency\Model\CurrencyInterface;
use Sylius\Component\Resource\Repository\RepositoryInterface;

/**
 * @author Anna Walasek <anna.walasek@lakion.com>
 */
final class OrderExchangeRateAndCurrencyUpdater implements OrderUpdaterInterface
{
    /**
     * @var CurrencyContextInterface
     */
    private $currencyContext;

    /**
     * @var RepositoryInterface
     */
    private $currencyRepository;

    /**
     * @var RepositoryInterface
     */
    private $cartRepository;

    /**
     * @param CurrencyContextInterface $currencyContext
     * @param RepositoryInterface $currencyRepository
     * @param RepositoryInterface $cartRepository
     */
    public function __construct(
        CurrencyContextInterface $currencyContext,
        RepositoryInterface $currencyRepository,
        RepositoryInterface $cartRepository
    ) {
        $this->currencyContext = $currencyContext;
        $this->currencyRepository = $currencyRepository;
        $this->cartRepository = $cartRepository;
    }

    /**
     * {@inheritdoc}
     */
    public function update(OrderInterface $order)
    {
        /** @var CurrencyInterface $currency */
        $currency = $this->currencyRepository->findOneBy(['code' => $this->currencyContext->getCurrencyCode()]);

        $order->setCurrencyCode($currency->getCode());
        $order->setExchangeRate($currency->getExchangeRate());

        $this->cartRepository->add($order);
    }
}
