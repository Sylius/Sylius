<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace spec\Sylius\Resource\Factory;

use PhpSpec\ObjectBehavior;
use spec\Sylius\Resource\Fixtures\SampleResource;
use Sylius\Resource\Factory\FactoryInterface;

require_once __DIR__.'/../Fixtures/SampleResource.php';

/**
 * @mixin \Sylius\Resource\Factory\Factory
 *
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 */
class FactorySpec extends ObjectBehavior
{
    function let()
    {
        $this->beConstructedWith('spec\Sylius\Resource\Fixtures\SampleResource');
    }

    function it_is_initializable()
    {
        $this->shouldHaveType('Sylius\Resource\Factory\Factory');
    }

    function it_implements_factory_interface()
    {
        $this->shouldHaveType(FactoryInterface::class);
    }

    function it_creates_a_new_instance_of_a_resource()
    {
        $this->createNew()->shouldHaveType(SampleResource::class);
    }
}
