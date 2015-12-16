<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace spec\Sylius\Component\Resource\Factory;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use spec\Sylius\Component\Resource\Fixtures\SampleResource;

require_once __DIR__.'/../Fixtures/SampleResource.php';

/**
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 */
class FactorySpec extends ObjectBehavior
{
    function let()
    {
        $this->beConstructedWith('spec\Sylius\Component\Resource\Fixtures\SampleResource');
    }

    function it_is_initializable()
    {
        $this->shouldHaveType('Sylius\Component\Resource\Factory\Factory');
    }

    function it_implements_factory_interface()
    {
        $this->shouldHaveType('Sylius\Component\Resource\Factory\FactoryInterface');
    }

    function it_creates_a_new_instance_of_a_resource()
    {
        $this->createNew()->shouldHaveType(SampleResource::class);
    }
}
