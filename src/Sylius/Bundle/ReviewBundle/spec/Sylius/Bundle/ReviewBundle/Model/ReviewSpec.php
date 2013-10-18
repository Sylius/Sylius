<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace spec\Sylius\Bundle\ReviewBundle\Model;

use PhpSpec\ObjectBehavior;

/**
 * @author Daniel Richter <nexyz9@gmail.com>
 */
class ReviewSpec extends ObjectBehavior
{
    public function it_should_be_initializable()
    {
        $this->shouldHaveType('Sylius\Bundle\ReviewBundle\Model\Review');
    }

    public function it_should_be_Sylius_review()
    {
        $this->shouldImplement('Sylius\Bundle\ReviewBundle\Model\ReviewInterface');
    }

    public function it_should_not_have_id_by_default()
    {
        $this->getId()->shouldReturn(null);
    }

    public function its_rating_should_be_mutable()
    {
        $this->setRating(1);
        $this->getRating()->shouldReturn(1);
    }
}
