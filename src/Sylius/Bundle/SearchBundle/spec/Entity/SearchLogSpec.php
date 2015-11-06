<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace spec\Sylius\Bundle\SearchBundle\Model;

use PhpSpec\ObjectBehavior;

/**
 * @author agounaris <agounaris@gmail.com>
 */
class SearchLogSpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->shouldHaveType('Sylius\Bundle\SearchBundle\Model\SearchLog');
    }

    function its_search_string_is_mutable()
    {
        $this->setSearchString('black');
        $this->getSearchString()->shouldReturn('black');
    }

    function its_remote_address_is_mutable()
    {
        $this->setRemoteAddress('100.100.100.100');
        $this->getRemoteAddress()->shouldReturn('100.100.100.100');
    }

    function its_created_at_is_mutable()
    {
        $this->setCreatedAt('2014-08-08 16:18:00');
        $this->getCreatedAt()->shouldReturn('2014-08-08 16:18:00');
    }
}
