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
use Sylius\Component\Core\Model\Product;

/**
 * @author agounaris <agounaris@gmail.com>
 */
class SearchIndexSpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->shouldHaveType('Sylius\Bundle\SearchBundle\Model\SearchIndex');
    }

    function its_item_id_is_mutable()
    {
        $this->setItemId(20);
        $this->getItemId()->shouldReturn(20);
    }

    function its_entity_is_mutable()
    {
        $this->setEntity(Product::class);
        $this->getEntity()->shouldReturn(Product::class);
    }

    function its_value_is_mutable()
    {
        $this->setValue('black t-shirt');
        $this->getValue()->shouldReturn('black t-shirt');
    }

    function its_tags_is_mutable()
    {
        $this->setTags('a:4:{s:6:"taxons";a:0:{}s:5:"price";i:5400;s:7:"made_of";a:0:{}s:5:"color";a:0:{}}');
        $this->getTags()->shouldReturn('a:4:{s:6:"taxons";a:0:{}s:5:"price";i:5400;s:7:"made_of";a:0:{}s:5:"color";a:0:{}}');
    }

    function its_created_at_is_mutable()
    {
        $this->setCreatedAt('2014-08-08 15:53:07');
        $this->getCreatedAt()->shouldReturn('2014-08-08 15:53:07');
    }
}
