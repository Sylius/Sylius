<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace spec\Sylius\Component\Review\Model;

use PhpSpec\ObjectBehavior;
use Sylius\Component\Core\Model\CustomerInterface;
use Sylius\Component\Core\Model\ProductInterface;

/**
 * @author Mateusz Zalewski <mateusz.zalewski@lakion.com>
 */
class ReviewSpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->shouldHaveType('Sylius\Component\Review\Model\Review');
    }

    function it_implements_review_interace()
    {
        $this->shouldImplement('Sylius\Component\Review\Model\ReviewInterface');
    }

    function it_has_title()
    {
        $this->setTitle('review title');
        $this->getTitle()->shouldReturn('review title');
    }

    function it_has_rating()
    {
        $this->setRating(5);
        $this->getRating()->shouldReturn(5);
    }

    function it_has_comment()
    {
        $this->setComment('Lorem ipsum dolor');
        $this->getComment()->shouldReturn('Lorem ipsum dolor');
    }

    function it_has_author(CustomerInterface $customer)
    {
        $this->setAuthor($customer);
        $this->getAuthor()->shouldReturn($customer);
    }

    function it_has_status()
    {
        $this->setStatus('new');
        $this->getStatus()->shouldReturn('new');
    }

    function it_has_product(ProductInterface $product)
    {
        $this->setProduct($product);
        $this->getProduct()->shouldReturn($product);
    }

    function it_has_created_at(\DateTime $createdAt)
    {
        $this->setCreatedAt($createdAt);
        $this->getCreatedAt()->shouldReturn($createdAt);
    }

    function it_has_updated_at(\DateTime $updatedAt)
    {
        $this->setUpdatedAt($updatedAt);
        $this->getUpdatedAt()->shouldReturn($updatedAt);
    }
}
