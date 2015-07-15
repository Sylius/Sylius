<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace spec\Sylius\Component\Storage;

use Doctrine\Common\Cache\Cache;
use PhpSpec\ObjectBehavior;

class DoctrineCacheStorageSpec extends ObjectBehavior
{
    public function let(Cache $cache)
    {
        $this->beConstructedWith($cache);
    }

    public function it_is_initializable()
    {
        $this->shouldHaveType('Sylius\Component\Storage\DoctrineCacheStorage');
    }

    public function it_implements_Sylius_storage_interface()
    {
        $this->shouldImplement('Sylius\Component\Storage\StorageInterface');
    }

    public function it_gets_default_data_if_no_record_was_found($cache)
    {
        $cache->fetch('key')->willReturn(null);

        $this->getData('key', 'default')->shouldReturn('default');
    }

    public function it_gets_data_if_found($cache)
    {
        $cache->fetch('key')->willReturn('data');

        $this->getData('key', 'default')->shouldReturn('data');
    }

    public function it_sets_data($cache)
    {
        $cache->save('key', 'data')->shouldBeCalled();

        $this->setData('key', 'data');
    }
}
