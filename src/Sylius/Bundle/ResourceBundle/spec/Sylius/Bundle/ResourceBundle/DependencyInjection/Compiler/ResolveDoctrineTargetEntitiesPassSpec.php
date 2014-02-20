<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace spec\Sylius\Bundle\ResourceBundle\DependencyInjection\Compiler;

use PhpSpec\ObjectBehavior;

/**
 * Compiler pass which resolves interfaces into target entity names during
 * compile time of container.
 *
 * @author Paweł Jędrzejewski <pjedrzejewski@diweb.pl>
 */
class ResolveDoctrineTargetEntitiesPassSpec extends ObjectBehavior
{
    function let()
    {
        $this->beConstructedWith('sylius_resource', array());
    }

    function it_is_initializable()
    {
        $this->shouldHaveType(
            'Sylius\Bundle\ResourceBundle\DependencyInjection\Compiler\ResolveDoctrineTargetEntitiesPass'
        );
    }

    function it_is_a_compiler_pass()
    {
        $this->shouldImplement('Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface');
    }
}
