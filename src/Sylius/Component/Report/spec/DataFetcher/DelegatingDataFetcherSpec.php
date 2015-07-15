<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace spec\Sylius\Component\Report\DataFetcher;

use PhpSpec\ObjectBehavior;
use Sylius\Component\Registry\ServiceRegistryInterface;
use Sylius\Component\Report\Model\ReportInterface;
use Sylius\Component\Report\DataFetcher\DataFetcherInterface;

/**
 * @author Łukasz Chruściel <lchrusciel@gmail.com>
 */
class DelegatingDataFetcherSpec extends ObjectBehavior
{
    public function let(ServiceRegistryInterface $serviceRegistryInterface)
    {
        $this->beConstructedWith($serviceRegistryInterface);
    }

    public function it_is_initializable()
    {
        $this->shouldHaveType('Sylius\Component\Report\DataFetcher\DelegatingDataFetcher');
    }

    public function it_implements_delegating_data_fetcher_interface()
    {
        $this->shouldImplement('Sylius\Component\Report\DataFetcher\DelegatingDataFetcherInterface');
    }

    public function it_delegates_data_fetcher_to_report(
        $serviceRegistryInterface,
        ReportInterface $subject,
        DataFetcherInterface $dataFetcher)
    {
        $subject->getDataFetcher()->willReturn('default_data_fetcher');
        $subject->getDataFetcherConfiguration()->willReturn(array());

        $serviceRegistryInterface->get('default_data_fetcher')->willReturn($dataFetcher);
        $dataFetcher->fetch(array())->shouldBeCalled()->willReturn(array(array('date' => '2014-12-31', 'user_total' => '20')));

        $this->fetch($subject)->shouldReturn(array(array('date' => '2014-12-31', 'user_total' => '20')));
    }

    public function it_should_complain_if_report_has_no_data_fetcher_defined(ReportInterface $subject)
    {
        $subject->getDataFetcher()->willReturn(null);

        $this
            ->shouldThrow(new \InvalidArgumentException('Cannot fetch data for ReportInterface instance without DataFetcher defined.'))
            ->duringFetch($subject)
        ;
    }
}
