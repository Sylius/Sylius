<?php

namespace spec\Sylius\Bundle\ResourceBundle\Twig;

use PhpSpec\ObjectBehavior;

/**
 * Sylius resource extension for Twig spec.
 *
 * @author Paweł Jędrzejewski <pjedrzejewski@diweb.pl>
 */
class SyliusResourceExtensionSpec extends ObjectBehavior
{
    /**
     * @param Symfony\Component\Routing\RouterInterface $router
     */
    function let($router)
    {
        $this->beConstructedWith($router);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType('Sylius\Bundle\ResourceBundle\Twig\SyliusResourceExtension');
    }

    function it_is_a_Twig_extension()
    {
        $this->shouldHaveType('Twig_Extension');
    }
}
