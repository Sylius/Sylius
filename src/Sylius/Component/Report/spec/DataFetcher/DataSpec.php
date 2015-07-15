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

/**
 * @author Łukasz Chruściel <lchrusciel@gmail.com>
 */
class DataSpec extends ObjectBehavior
{
    public function it_is_initializable()
    {
        $this->shouldHaveType('Sylius\Component\Report\DataFetcher\Data');
    }

    public function it_has_label_ifnromation()
    {
        $this->setLabels(array());
        $this->getLabels()->shouldReturn(array());
    }

    public function it_has_data()
    {
        $this->setData(array());
        $this->getData()->shouldReturn(array());
    }
}
