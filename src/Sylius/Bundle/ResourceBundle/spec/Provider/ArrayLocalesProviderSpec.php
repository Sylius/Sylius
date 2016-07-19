<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace spec\Sylius\Bundle\ResourceBundle\Provider;

use PhpSpec\ObjectBehavior;

/**
 * @author Anna Walasek <anna.walasek@lakion.com>
 */
final class ArrayLocalesProviderSpec extends ObjectBehavior
{
    function let()
    {
        $this->beConstructedWith(['en_US', 'pl_PL']);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType('Sylius\Bundle\ResourceBundle\Provider\ArrayLocalesProvider');
    }

    function it_returns_available_locales()
    {
        $this->getAvailableLocales()->shouldReturn(['en_US', 'pl_PL']);
    }
}
