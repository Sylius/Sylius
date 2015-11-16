<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace spec\Sylius\Component\Resource\EventDispatcher;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Sylius\Component\Resource\Model\ResourceInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

/**
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 */
class ResourceMetadataSpec extends ObjectBehavior
{
    function let()
    {
        $this->beConstructedwith(
            'sylius',
            'banana',
            array(),
            'doctrine/orm',
            'SyliusAdminBundle'
        );
    }

    function it_is_initializable()
    {
        $this->shouldHaveType('Sylius\Component\Resource\Metadata\ResourceMetadata');
    }
}
