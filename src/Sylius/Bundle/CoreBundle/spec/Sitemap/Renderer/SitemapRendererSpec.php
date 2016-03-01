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
use Sylius\Bundle\CoreBundle\Sitemap\Model\SitemapInterface;
use Sylius\Bundle\CoreBundle\Sitemap\Renderer\RendererAdapterInterface;
use Sylius\Bundle\CoreBundle\Sitemap\Renderer\SitemapRendererInterface;

/**
 * @author Arkadiusz Krakowiak <arkadiusz.krakowiak@lakion.com>
 */
class SitemapRendererSpec extends ObjectBehavior
{
    function let(RendererAdapterInterface $adapter)
    {
        $this->beConstructedWith($adapter);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType('Sylius\Bundle\CoreBundle\Sitemap\Renderer\SitemapRenderer');
    }

    function it_implements_sitemap_renderer_interface()
    {
        $this->shouldImplement(SitemapRendererInterface::class);
    }

    function it_renders_sitemap($adapter, SitemapInterface $sitemap)
    {
        $adapter->render($sitemap)->shouldBeCalled();

        $this->render($sitemap);
    }
}
