<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
 
namespace spec\Sylius\CoreBundle\Sitemap\Builder;
 
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Sylius\CoreBundle\Sitemap\Builder\SitemapBuilderInterface;
use Sylius\CoreBundle\Sitemap\Factory\SitemapFactoryInterface;
use Sylius\CoreBundle\Sitemap\Model\SitemapInterface;
use Sylius\CoreBundle\Sitemap\Model\SitemapUrlInterface;
use Sylius\CoreBundle\Sitemap\Provider\UrlProviderInterface;

/**
 * @author Arkadiusz Krakowiak <arkadiusz.krakowiak@lakion.com>
 */
class SitemapBuilderSpec extends ObjectBehavior
{
    function let(SitemapFactoryInterface $sitemapFactory)
    {
        $this->beConstructedWith($sitemapFactory);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType('Sylius\CoreBundle\Sitemap\Builder\SitemapBuilder');
    }

    function it_implements_sitemap_builder_interface()
    {
        $this->shouldImplement(SitemapBuilderInterface::class);
    }

    function it_builds_sitemap(
        $sitemapFactory,
        UrlProviderInterface $productUrlProvider,
        UrlProviderInterface $staticUrlProvider,
        SitemapInterface $sitemap,
        SitemapUrlInterface $bookUrl,
        SitemapUrlInterface $homePage
    ) {
        $sitemapFactory->createNew()->willReturn($sitemap);
        $this->addProvider($productUrlProvider);
        $this->addProvider($staticUrlProvider);
        $productUrlProvider->generate()->willReturn([$bookUrl]);
        $staticUrlProvider->generate()->willReturn([$homePage]);

        $sitemap->setUrls([$bookUrl, $homePage])->shouldBeCalled();

        $this->build()->shouldReturn($sitemap);
    }
}
