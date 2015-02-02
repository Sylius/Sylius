<?php

namespace Sylius\Bundle\CoreBundle\DataFetcher;

use Sylius\Bundle\CoreBundle\Doctrine\ORM\OrderRepository;
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
     * @var OrderRepository
     */
    private $orderRepository;

    public function __construct(OrderRepository $orderRepository)
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
