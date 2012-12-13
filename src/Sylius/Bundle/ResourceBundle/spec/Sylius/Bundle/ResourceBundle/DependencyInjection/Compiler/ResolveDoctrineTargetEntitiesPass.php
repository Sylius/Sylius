<?php

namespace spec\Sylius\Bundle\ResourceBundle\DependencyInjection\Compiler;

use PHPSpec2\ObjectBehavior;

/**
 * Compiler pass which resolves interfaces into target entity names during
 * compile time of container.
 *
 * @author Paweł Jędrzejewski <pjedrzejewski@diweb.pl>
 */
class ResolveDoctrineTargetEntitiesPass extends ObjectBehavior
{
    function let()
    {
        $this->beConstructedWith('sylius_resource', array());
    }

    function it_should_be_initializable()
    {
        $this->shouldHaveType('Sylius\Bundle\ResourceBundle\DependencyInjection\Compiler\ResolveDoctrineTargetEntitiesPass');
    }

    function it_should_be_a_compiler_pass()
    {
        $this->shouldImplement('Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface');
    }
}
