<?php

namespace spec\Sylius\Bundle\ResourceBundle\Controller;

use PhpSpec\ObjectBehavior;

use Sylius\Bundle\ResourceBundle\Controller\Configuration;

/**
 * Resource controller spec.
 *
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 */
class ResourceControllerSpec extends ObjectBehavior
{
    public function let(Configuration $configuration)
    {
        $this->beConstructedWith($configuration);
    }

    public function it_is_initializable()
    {
        $this->shouldHaveType('Sylius\Bundle\ResourceBundle\Controller\ResourceController');
    }

    public function it_is_a_controller()
    {
        $this->shouldHaveType('Symfony\Bundle\FrameworkBundle\Controller\Controller');
    }
}
