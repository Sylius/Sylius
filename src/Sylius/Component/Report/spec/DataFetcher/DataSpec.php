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
 * @author Łukasz Chruściel <lukasz.chrusciel@lakion.com>
 */
final class DataSpec extends ObjectBehavior
{
    public function it_is_initializable()
    {
        $this->shouldHaveType('Sylius\Component\Report\DataFetcher\Data');
    }

    public function it_has_label_information()
    {
        $this->setLabels([]);
        $this->getLabels()->shouldReturn([]);
    }

    public function it_has_data()
    {
        $this->setData([]);
        $this->getData()->shouldReturn([]);
    }
}
