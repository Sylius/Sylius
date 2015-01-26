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
use Prophecy\Argument;

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

    function it_fetches_data_by_day($userRepository)
    {
        $rawData = array(array('date' => '2014-12-31', 'user_total' => '20'),array('date' => '2015-01-01', 'user_total' => '2'));
     
        $configuration = array(
            'start' => new \DateTime('2014-12-31 00:00:00.000000'),
            'end' => new \DateTime('2015-01-01 00:00:00.000000'),
            'period' => 'day',
            'empty_records' => false );
     
        $userRepository->getRegistrationStatistic(Argument::type('array'))->willReturn($rawData);

        $data = new Data();
        $data->setLabels(array_keys($rawData[0]));
        $data->setData(array('2014-12-31' => '20', '2015-01-01' => '2'));

        $this->fetch($configuration)->shouldBeLike($data);
    }

    function it_fetches_data_by_month($userRepository)
    {
        $rawData = array(array('date' => '2014-12-30', 'user_total' => '20'),array('date' => '2015-01-01', 'user_total' => '2'));
     
        $configuration = array(
            'start' => new \DateTime('2014-12-01 00:00:00.000000'),
            'end' => new \DateTime('2015-01-03 00:00:00.000000'),
            'period' => 'month',
            'empty_records' => false );
     
        $userRepository->getRegistrationStatistic(Argument::type('array'))->willReturn($rawData);

        $data = new Data();
        $data->setLabels(array_keys($rawData[0]));
        $data->setData(array('December 2014' => '20', 'January 2015' => '2'));

        $this->fetch($configuration)->shouldBeLike($data);
    }

    function it_fetches_data_by_year($userRepository)
    {
        $rawData = array(array('date' => '2014-01-01', 'user_total' => '20'),array('date' => '2015-01-30', 'user_total' => '2'));
     
        $configuration = array(
            'start' => new \DateTime('2014-12-31 00:00:00.000000'),
            'end' => new \DateTime('2015-01-01 00:00:00.000000'),
            'period' => 'year',
            'empty_records' => false );
     
        $userRepository->getRegistrationStatistic(Argument::type('array'))->willReturn($rawData);

        $data = new Data();
        $data->setLabels(array_keys($rawData[0]));
        $data->setData(array('2014' => '20', '2015' => '2'));

        $this->fetch($configuration)->shouldBeLike($data);
    }

    function it_fills_gaps($userRepository)
    {
        $rawData = array(array('date' => '2014-12-30', 'user_total' => '20'),array('date' => '2015-01-01', 'user_total' => '2'));
     
        $configuration = array(
            'start' => new \DateTime('2014-11-01 00:00:00.000000'),
            'end' => new \DateTime('2015-01-03 00:00:00.000000'),
            'period' => 'month',
            'empty_records' => true );
     
        $userRepository->getRegistrationStatistic(Argument::type('array'))->willReturn($rawData);

        $data = new Data();
        $data->setLabels(array_keys($rawData[0]));
        $data->setData(array('November 2014' => '0', 'December 2014' => '20', 'January 2015' => '2'));

        $this->fetch($configuration)->shouldBeLike($data);
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