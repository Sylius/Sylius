<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
 
namespace spec\Sylius\Bundle\CoreBundle\Sitemap\Builder;
 
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Sylius\Bundle\CoreBundle\Sitemap\Factory\SitemapFactoryInterface;
use Sylius\Bundle\CoreBundle\Sitemap\Model\SitemapInterface;
use Sylius\Bundle\CoreBundle\Sitemap\Model\SitemapUrlInterface;
use Sylius\Bundle\CoreBundle\Sitemap\Provider\UrlProviderInterface;

/**
 * @author Arkadiusz Krakowiak <arkadiusz.krakowiak@lakion.com>
 */
class SitemapBuilderSpec extends ObjectBehavior
{
    function let(
        SitemapFactoryInterface $sitemapFactory
    ) {
        $this->beConstructedWith(
            $sitemapFactory
        );
    }

    function it_is_initializable()
    {
        $this->shouldHaveType('Sylius\Bundle\CoreBundle\Sitemap\Builder\SitemapBuilder');
    }

    function it_implements_sitemap_builder_interface()
    {
        $this->shouldImplement('Sylius\Bundle\CoreBundle\Sitemap\Builder\SitemapBuilderInterface');
    }

    function it_builds_sitemap(
        $sitemapFactory,
        UrlProviderInterface $productUrlProvider,
        UrlProviderInterface $staticUrlProvider,
        SitemapInterface $sitemap,
        SitemapUrlInterface $bookUrl,
        SitemapUrlInterface $homePage
    ) {
        $sitemapFactory->createEmpty()->willReturn($sitemap);
        $this->addProvider($productUrlProvider);
        $this->addProvider($staticUrlProvider);
        $productUrlProvider->generate(array())->willReturn(array($bookUrl));
        $staticUrlProvider->generate(array())->willReturn(array($homePage));

        $array = array_merge(array($bookUrl), array($homePage));
        $sitemap->setUrls($array)->shouldBeCalled();

        $this->build();
    }
}
