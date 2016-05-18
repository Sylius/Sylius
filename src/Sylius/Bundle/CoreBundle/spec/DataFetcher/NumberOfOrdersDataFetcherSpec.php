<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace spec\Sylius\Bundle\CoreBundle\DataFetcher;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Sylius\Bundle\ReportBundle\DataFetcher\TimePeriod;
use Sylius\Component\Core\Repository\OrderRepositoryInterface;
use Sylius\Component\Report\DataFetcher\Data;
use Sylius\Component\Report\DataFetcher\DataFetcherInterface;
use Sylius\Component\Report\DataFetcher\DefaultDataFetchers;

/**
 * @author Łukasz Chruściel <lukasz.chrusciel@lakion.com>
 */
class NumberOfOrdersDataFetcherSpec extends ObjectBehavior
{
    public function it_is_initializable()
    {
        $this->shouldHaveType('Sylius\Bundle\CoreBundle\DataFetcher\NumberOfOrdersDataFetcher');
    }

    public function it_extends_time_period()
    {
        $this->shouldHaveType(TimePeriod::class);
    }

    public function it_implements_data_fetcher_interface()
    {
        $this->shouldImplement(DataFetcherInterface::class);
    }

    public function let(OrderRepositoryInterface $orderRepository)
    {
        $this->beConstructedWith($orderRepository);
    }

    public function it_has_type()
    {
        $this->getType()->shouldReturn(DefaultDataFetchers::NUMBER_OF_ORDERS);
    }

    public function it_fetches_data_by_day($orderRepository)
    {
        $rawData = [['date' => '2014-12-31', 'number_of_orders' => '20'], ['date' => '2015-01-01', 'number_of_orders' => '2']];

        $configuration = [
            'start' => new \DateTime('2014-12-31 00:00:00.000000'),
            'end' => new \DateTime('2015-01-01 00:00:00.000000'),
            'period' => 'day',
            'empty_records' => false, ];

        $orderRepository->ordersBetweenDatesGroupByDate(Argument::type('array'))->willReturn($rawData);

        $data = new Data();
        $data->setLabels(array_keys($rawData[0]));
        $data->setData(['2014-12-31' => '20', '2015-01-01' => '2']);

        $this->fetch($configuration)->shouldBeLike($data);
    }

    public function it_fetches_data_by_month($orderRepository)
    {
        $rawData = [['date' => '2014-12-30', 'number_of_orders' => '20'], ['date' => '2015-01-01', 'number_of_orders' => '2']];

        $configuration = [
            'start' => new \DateTime('2014-12-01 00:00:00.000000'),
            'end' => new \DateTime('2015-01-03 00:00:00.000000'),
            'period' => 'month',
            'empty_records' => false, ];

        $orderRepository->ordersBetweenDatesGroupByDate(Argument::type('array'))->willReturn($rawData);

        $data = new Data();
        $data->setLabels(array_keys($rawData[0]));
        $data->setData(['December 2014' => '20', 'January 2015' => '2']);

        $this->fetch($configuration)->shouldBeLike($data);
    }

    public function it_fetches_data_by_year($orderRepository)
    {
        $rawData = [['date' => '2014-01-01', 'number_of_orders' => '20'], ['date' => '2015-01-30', 'number_of_orders' => '2']];

        $configuration = [
            'start' => new \DateTime('2014-12-31 00:00:00.000000'),
            'end' => new \DateTime('2015-01-01 00:00:00.000000'),
            'period' => 'year',
            'empty_records' => false, ];

        $orderRepository->ordersBetweenDatesGroupByDate(Argument::type('array'))->willReturn($rawData);

        $data = new Data();
        $data->setLabels(array_keys($rawData[0]));
        $data->setData(['2014' => '20', '2015' => '2']);

        $this->fetch($configuration)->shouldBeLike($data);
    }

    public function it_fills_gaps($orderRepository)
    {
        $rawData = [['date' => '2014-12-30', 'number_of_orders' => '20'], ['date' => '2015-01-01', 'number_of_orders' => '2']];

        $configuration = [
            'start' => new \DateTime('2014-11-01 00:00:00.000000'),
            'end' => new \DateTime('2015-01-03 00:00:00.000000'),
            'period' => 'month',
            'empty_records' => true, ];

        $orderRepository->ordersBetweenDatesGroupByDate(Argument::type('array'))->willReturn($rawData);

        $data = new Data();
        $data->setLabels(array_keys($rawData[0]));
        $data->setData(['November 2014' => '0', 'December 2014' => '20', 'January 2015' => '2']);

        $this->fetch($configuration)->shouldBeLike($data);
    }

    public function it_does_not_allowed_wrong_data_period($orderRepository, Data $data)
    {
        $configuration = [
            'start' => new \DateTime('2010-01-01 00:00:00.000000'),
            'end' => new \DateTime('2012-01-01 00:00:00.000000'),
            'period' => 3,
            'empty_records' => false, ];
        $this
            ->shouldThrow(new \InvalidArgumentException('Wrong data fetcher period'))
            ->duringFetch($configuration)
        ;
    }
}
