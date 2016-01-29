<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace spec\Sylius\Component\Product\Model;

use PhpSpec\ObjectBehavior;

class DateRangeSpec extends ObjectBehavior
{
    function let(\DateTime $start, \DateTime $end)
    {
        $this->beConstructedWith($start, $end);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType('Sylius\Component\Product\Model\DateRange');
    }

    function its_start_date_is_mutable()
    {
        $date = new \DateTime('last year');

        $this->setStart($date);
        $this->getStart()->shouldReturn($date);
    }

    function its_end_date_is_mutable()
    {
        $date = new \DateTime('next year');

        $this->setEnd($date);
        $this->getEnd()->shouldReturn($date);
    }

    function it_checks_whether_current_datetime_is_in_range_of_start_and_end()
    {
        $start = new \DateTime('last year');
        $end = new \DateTime('next year');
        $this->setStart($start);
        $this->setEnd($end);
        $this->isInRange()->shouldReturn(true);

        $this->setStart(null);
        $this->isInRange()->shouldReturn(true);

        $this->setEnd(null);
        $this->isInRange()->shouldReturn(true);

        $this->setStart($end);
        $this->isInRange()->shouldReturn(false);
    }
}
