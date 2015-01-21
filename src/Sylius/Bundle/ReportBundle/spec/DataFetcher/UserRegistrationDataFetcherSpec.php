<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace spec\Sylius\Bundle\ReportBundle\DataFetcher;

use PhpSpec\ObjectBehavior;

use Sylius\Bundle\CoreBundle\Doctrine\ORM\UserRepository;
use Sylius\Component\Report\DataFetcher\Data;

/**
 * @author Łukasz Chruściel <lukasz.chrusciel@lakion.com>
 */
class UserRegistrationDataFetcherSpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->shouldHaveType('Sylius\Bundle\ReportBundle\DataFetcher\UserRegistrationDataFetcher');
    }

    function it_implements_data_fetcher_interface()
    {
        $this->shouldImplement('Sylius\Component\Report\DataFetcher\DataFetcherInterface');
    }

    function let(UserRepository $userRepository)
    {
        $this->beConstructedWith($userRepository);
    }

    function it_has_type()
    {
        $this->getType()->shouldReturn('user_registration');
    }

    function it_fetches_data_by_day($userRepository, Data $data)
    {
        $rawData = array(array('date' => 'January 2014', 'user_total' => '2'));
        $configuration = array(
            'start' => new \DateTime('2010-01-01 00:00:00.000000'),
            'end' => new \DateTime('2012-01-01 00:00:00.000000'),
            'period' => 'day',
            'empty_records' => false );
        $userRepository->getDailyStatistic($configuration)->shouldBeCalled();
        $userRepository->getDailyStatistic($configuration)->willReturn($rawData);
        $this->fetch($configuration);
    }

    function it_fetches_data_by_month($userRepository, Data $data)
    {
        $rawData = array(array('date' => 'January 2014', 'user_total' => '2'));
        $configuration = array(
            'start' => new \DateTime('2010-01-01 00:00:00.000000'),
            'end' => new \DateTime('2012-01-01 00:00:00.000000'),
            'period' => 'month',
            'empty_records' => false );
        $userRepository->getMonthlyStatistic($configuration)->shouldBeCalled();
        $userRepository->getMonthlyStatistic($configuration)->willReturn($rawData);
        $this->fetch($configuration);
    }

    function it_fetches_data_by_year($userRepository, Data $data)
    {
        $rawData = array(array('date' => '2014', 'user_total' => '2'));
        $configuration = array(
            'start' => new \DateTime('2010-01-01 00:00:00.000000'),
            'end' => new \DateTime('2012-01-01 00:00:00.000000'),
            'period' => 'year',
            'empty_records' => false );
        $userRepository->getYearlyStatistic($configuration)->shouldBeCalled();
        $userRepository->getYearlyStatistic($configuration)->willReturn($rawData);
        $this->fetch($configuration);
    }

    function it_does_not_allowed_wrond_data_period($userRepository, Data $data)
    {
        $configuration = array(
            'start' => new \DateTime('2010-01-01 00:00:00.000000'),
            'end' => new \DateTime('2012-01-01 00:00:00.000000'),
            'period' => 3,
            'empty_records' => false );
        $this
            ->shouldThrow(new \InvalidArgumentException('Wrong data fetcher period'))
            ->duringFetch($configuration)
        ;
    }
}