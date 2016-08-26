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
use Sylius\Component\Currency\Model\CurrencyInterface;
use Sylius\Component\Resource\Repository\RepositoryInterface;
use Webmozart\Assert\Assert;

/**
 * @author Jan Góralski <jan.goralski@lakion.com>
 */
final class OrderExchangeRateUpdater implements OrderUpdaterInterface
{
    /**
     * @var RepositoryInterface
     */
    private $currencyRepository;

    /**
     * @param RepositoryInterface $currencyRepository
     */
    public function __construct(RepositoryInterface $currencyRepository)
    {
        $this->currencyRepository = $currencyRepository;
    }

    /**
     * {@inheritdoc}
     */
    public function update(OrderInterface $order)
    {
        $currencyCode = $order->getCurrencyCode();
        /** @var CurrencyInterface $currency */
        $currency = $this->currencyRepository->findOneBy(['code' => $currencyCode]);

        Assert::notNull($currency);

        $order->setExchangeRate($currency->getExchangeRate());
    }
}
