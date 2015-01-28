<?php

namespace Sylius\Bundle\ReportBundle\DataFetcher;

use Sylius\Bundle\CoreBundle\Doctrine\ORM\OrderRepository;
use Sylius\Component\Report\DataFetcher\DefaultDataFetchers;

/**
 * Number of orders data fetcher
 *
 * @author Łukasz Chruściel <lukasz.chrusciel@lakion.com>
 */
class NumberOfOrdersDataFetcher extends TimePeriod
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
        return $this->orderRepository->ordersBetweenDatesGroupByDate($configuration);
    }

    /**
     * {@inheritdoc}
     */
    public function getType()
    {
        return DefaultDataFetchers::NUMBER_OF_ORDERS;
    }
}
