<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\CoreBundle\DataFetcher;

use Sylius\Bundle\ReportBundle\DataFetcher\TimePeriod;
use Sylius\Component\Core\Repository\OrderRepositoryInterface;
use Sylius\Component\Currency\Provider\CurrencyProviderInterface;
use Sylius\Component\Report\DataFetcher\DefaultDataFetchers;

/**
 * Sales total data fetcher
 *
 * @author Łukasz Chruściel <lukasz.chrusciel@lakion.com>
 * @author Fernando Caraballo Ortiz <caraballo.ortiz@gmail.com>
 */
class SalesTotalDataFetcher extends TimePeriod
{
    /**
     * @var OrderRepositoryInterface
     */
    private $orderRepository;

    /**
     * @var CurrencyProviderInterface
     */
    private $currencyProvider;

    /**
     * @param OrderRepositoryInterface  $orderRepository
     * @param CurrencyProviderInterface $currencyProvider
     */
    public function __construct(OrderRepositoryInterface $orderRepository, CurrencyProviderInterface $currencyProvider)
    {
        $this->orderRepository  = $orderRepository;
        $this->currencyProvider = $currencyProvider;
    }

    /**
     * {@inheritdoc}
     */
    protected function getData(array $configuration = array())
    {
        return $this->orderRepository->revenueBetweenDatesGroupByDate(
            $configuration, $this->currencyProvider->getBaseCurrency()
        );
    }

    /**
     * {@inheritdoc}
     */
    public function getType()
    {
        return DefaultDataFetchers::SALES_TOTAL;
    }
}
