<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
 
namespace spec\Sylius\Bundle\CoreBundle\Sitemap\Renderer;
 
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Symfony\Bundle\TwigBundle\TwigEngine;

/**
 * @author Arkadiusz Krakowiak <arkadiusz.krakowiak@lakion.com>
 */
class TwigAdapterSpec extends ObjectBehavior
{
    function let(TwigEngine $twig)
    {
        $this->beConstructedWith($twig);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType('Sylius\Bundle\CoreBundle\Sitemap\Renderer\TwigAdapter');
    }

    function it_implements_renderer_adapter_interface()
    {
        $this->shouldImplement('Sylius\Bundle\CoreBundle\Sitemap\Renderer\RendererAdapterInterface');
    }

    function it_renders_sitemap($twig)
    {
        $twig->renderResponse('SyliusCoreBundle:Sitemap:sitemap.xml.twig', array('data' => Argument::any()))->shouldBeCalled();

        $this->render('SyliusCoreBundle:Sitemap:sitemap.xml.twig', array('data' => Argument::any()));
    }
}
