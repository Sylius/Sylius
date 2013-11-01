<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace spec\Sylius\Bundle\OmnipayBundle;

use PHPSpec2\ObjectBehavior;

class SyliusOmnipayBundle extends ObjectBehavior
{
    public function it_should_be_initializable()
    {
        $this->shouldHaveType('Sylius\Bundle\OmnipayBundle\SyliusOmnipayBundle');
    }

    public function it_should_be_a_bundle()
    {
        $this->shouldHaveType('Symfony\Component\HttpKernel\Bundle\Bundle');
    }
}
