<?php

namespace spec\Sylius\Bundle\ResourceBundle\DependencyInjection;

use PHPSpec2\ObjectBehavior;

/**
 * Doctrine target entities resolver spec.
 * It adds proper method calls to doctrine listener.
 *
 * @author Paweł Jędrzejewski <pjedrzejewski@diweb.pl>
 */
class DoctrineTargetEntitiesResolver extends ObjectBehavior
{
    function it_should_be_initializable()
    {
        $this->shouldHaveType('Sylius\Bundle\ResourceBundle\DependencyInjection\DoctrineTargetEntitiesResolver');
    }
}
