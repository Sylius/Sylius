<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace spec\Sylius\Bundle\ResourceBundle\Storage;

use PhpSpec\ObjectBehavior;
use Sylius\Component\Resource\Storage\StorageInterface;

/**
 * @author Kamil Kokot <kamil@kokot.me>
 */
final class CookieStorageSpec extends ObjectBehavior
{
    function it_is_a_storage()
    {
        $this->shouldImplement(StorageInterface::class);
    }

    function it_does_not_have_a_named_value_if_it_was_not_set_previously()
    {
        $this->get('name')->shouldReturn(null);
        $this->has('name')->shouldReturn(false);
    }

    function it_stores_a_named_value()
    {
        $this->set('name', 'value');

        $this->get('name')->shouldReturn('value');
        $this->has('name')->shouldReturn(true);
    }

    function it_removes_a_stored_named_value()
    {
        $this->set('name', 'value');
        $this->remove('name');

        $this->get('name')->shouldReturn(null);
        $this->has('name')->shouldReturn(false);
    }

    function it_returns_default_value_if_none_found()
    {
        $this->get('name', 'default')->shouldReturn('default');
    }

    function it_returns_all_values()
    {
        $this->set('foo', 'bar');
        $this->set('buzz', 'lightyear');

        $this->all()->shouldReturn([
            'foo' => 'bar',
            'buzz' => 'lightyear',
        ]);
    }
}
