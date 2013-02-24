<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace spec\Sylius\Bundle\TaxonomiesBundle\DependencyInjection;

use PHPSpec2\ObjectBehavior;

/**
 * Taxonomies bundle extension.
 *
 * @author Paweł Jędrzejewski <pjedrzejewski@diweb.pl>
 */
class SyliusTaxonomiesExtension extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->shouldHaveType('Sylius\Bundle\TaxonomiesBundle\DependencyInjection\SyliusTaxonomiesExtension');
    }

    function it_is_a_container_extension()
    {
        $this->shouldHaveType('Symfony\Component\HttpKernel\DependencyInjection\Extension');
    }
}
