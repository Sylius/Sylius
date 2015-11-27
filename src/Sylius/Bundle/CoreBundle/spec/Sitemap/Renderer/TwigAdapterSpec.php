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
use Sylius\Bundle\CoreBundle\Sitemap\Model\SitemapInterface;
use Sylius\Bundle\CoreBundle\Sitemap\Model\SitemapUrlInterface;
use Symfony\Component\Templating\EngineInterface;

/**
 * @author Arkadiusz Krakowiak <arkadiusz.krakowiak@lakion.com>
 */
class TwigAdapterSpec extends ObjectBehavior
{
    function let(EngineInterface $twig)
    {
        $this->beConstructedWith($twig, array('template' => '@SyliusCore/Sitemap/url_set.xml.twig'));
    }

    function it_is_initializable()
    {
        $this->shouldHaveType('Sylius\Bundle\CoreBundle\Sitemap\Renderer\TwigAdapter');
    }

    function it_implements_renderer_adapter_interface()
    {
        $this->shouldImplement('Sylius\Bundle\CoreBundle\Sitemap\Renderer\RendererAdapterInterface');
    }

    function it_renders_sitemap(
        $twig,
        SitemapInterface $sitemap,
        SitemapUrlInterface $productUrl
    ) {
        $sitemap->getUrls()->willReturn(array($productUrl));
        $twig->render('@SyliusCore/Sitemap/url_set.xml.twig', array('url_set' => array($productUrl)))->shouldBeCalled();

        $this->render($sitemap);
    }
}
