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

use Sylius\Component\Core\Repository\OrderRepositoryInterface;
use Sylius\Bundle\ReportBundle\DataFetcher\TimePeriod;
use Sylius\Component\Report\DataFetcher\DefaultDataFetchers;

/**
 * Sales total data fetcher
 *
 * @author Łukasz Chruściel <lukasz.chrusciel@lakion.com>
 */
class SalesTotalDataFetcher extends TimePeriod
{
    /**
     * @var OrderRepositoryInterface
     */
    private $orderRepository;

    public function __construct(OrderRepositoryInterface $orderRepository)
    {
        $this->orderRepository = $orderRepository;
    }

    /**
     * {@inheritdoc}
     */
    protected function getData(array $configuration = array())
    {
        return $this->orderRepository->revenueBetweenDatesGroupByDate($configuration);
    }

    /**
     * {@inheritdoc}
     */
    public function getType()
    {
        return DefaultDataFetchers::SALES_TOTAL;
    }
}
