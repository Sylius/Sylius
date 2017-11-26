<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace spec\Sylius\Bundle\ResourceBundle\Storage;

use PhpSpec\ObjectBehavior;
use Sylius\Component\Resource\Storage\StorageInterface;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\HttpFoundation\Session\Storage\MockArraySessionStorage;

final class SessionStorageSpec extends ObjectBehavior
{
    public function let(): void
    {
        $this->beConstructedWith(new Session(new MockArraySessionStorage()));
    }

    public function it_is_a_storage(): void
    {
        $this->shouldImplement(StorageInterface::class);
    }

    public function it_does_not_have_a_named_value_if_it_was_not_set_previously(): void
    {
        $this->get('name')->shouldReturn(null);
        $this->has('name')->shouldReturn(false);
    }

    public function it_stores_a_named_value(): void
    {
        $this->set('name', 'value');

        $this->get('name')->shouldReturn('value');
        $this->has('name')->shouldReturn(true);
    }

    public function it_removes_a_stored_named_value(): void
    {
        $this->set('name', 'value');
        $this->remove('name');

        $this->get('name')->shouldReturn(null);
        $this->has('name')->shouldReturn(false);
    }

    public function it_returns_default_value_if_none_found(): void
    {
        $this->get('name', 'default')->shouldReturn('default');
    }

    public function it_returns_all_values(): void
    {
        $this->set('foo', 'bar');
        $this->set('buzz', 'lightyear');

        $this->all()->shouldReturn([
            'foo' => 'bar',
            'buzz' => 'lightyear',
        ]);
    }
}
