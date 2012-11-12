<?php

namespace spec\Sylius\Bundle\ResourceBundle\Controller;

use PHPSpec2\ObjectBehavior;

/**
 * Resource controller spec.
 *
 * @author Paweł Jędrzejewski <pjedrzejewski@diweb.pl>
 */
class ResourceController extends ObjectBehavior
{
    function let()
    {
        $this->beConstructedWith('sylius_resource', 'test', 'SyliusResourceBundle:Test');
    }

    function it_should_be_initializable()
    {
        $this->shouldHaveType('Sylius\Bundle\ResourceBundle\Controller\ResourceController');
    }
}
