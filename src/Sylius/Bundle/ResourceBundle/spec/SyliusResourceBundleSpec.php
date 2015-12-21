<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace spec\Sylius\Bundle\ResourceBundle;

use PhpSpec\ObjectBehavior;
use Symfony\Component\HttpKernel\Bundle\Bundle;

/**
 * Sylius resource bundle spec.
 *
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 */
class SyliusResourceBundleSpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->shouldHaveType('Sylius\Bundle\ResourceBundle\SyliusResourceBundle');
    }

    function it_is_a_bundle()
    {
        $this->shouldHaveType(Bundle::class);
    }
}
