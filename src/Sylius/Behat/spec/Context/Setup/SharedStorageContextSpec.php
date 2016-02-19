<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace spec\Sylius\Behat\Context\Setup;

use Behat\Behat\Context\Context;
use PhpSpec\ObjectBehavior;
use Sylius\Component\Core\Test\Services\SharedStorageInterface;

/**
 * @author Mateusz Zalewski <mateusz.zalewski@lakion.com>
 */
class SharedStorageContextSpec extends ObjectBehavior
{
    function let(SharedStorageInterface $sharedStorage)
    {
        $this->beConstructedWith($sharedStorage);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType('Sylius\Behat\Context\Setup\SharedStorageContext');
    }

    function it_implements_context_interface()
    {
        $this->shouldImplement(Context::class);
    }

    function it_transforms_it_word_into_latest_resource($sharedStorage)
    {
        $sharedStorage->getLatestResource()->willReturn('string');

        $this->getLatestResource()->shouldReturn('string');
    }

    function it_transform_this_and_that_with_resource_name_to_current_resource_of_this_type($sharedStorage)
    {
        $sharedStorage->get('customer')
    }
}
