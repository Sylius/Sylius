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

/**
 * @author Łukasz Chruściel <lchrusciel@gmail.com>
 */
class DelegatingDataFetcherSpec extends ObjectBehavior
{
    function let(ServiceRegistryInterface $serviceRegistryInterface)
    {
        $this->beConstructedWith($serviceRegistryInterface);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType('Sylius\Component\Report\DataFetcher\DelegatingDataFetcher');
    }

    function it_implements_delegating_data_fetcher_interface()
    {
        $this->shouldImplement('Sylius\Component\Report\DataFetcher\DelegatingDataFetcherInterface');
    }

    function it_should_complain_if_report_has_no_data_fetcher_defined(ReportInterface $subject)
    {
        $subject->getDataFetcher()->willReturn(null);

        $this
            ->shouldThrow(new \InvalidArgumentException('Cannot fetch data for ReportInterface instance without DataFetcher defined.'))
            ->duringFetch($subject)
        ;
    }
}