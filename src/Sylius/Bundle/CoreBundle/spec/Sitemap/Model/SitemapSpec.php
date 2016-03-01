<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
 
namespace spec\Sylius\Bundle\CoreBundle\Sitemap\Model;
 
use PhpSpec\ObjectBehavior;
use Sylius\Bundle\CoreBundle\Sitemap\Exception\SitemapUrlNotFoundException;
use Sylius\Bundle\CoreBundle\Sitemap\Model\SitemapInterface;
use Sylius\Bundle\CoreBundle\Sitemap\Model\SitemapUrlInterface;

/**
 * @author Arkadiusz Krakowiak <arkadiusz.krakowiak@lakion.com>
 */
class SitemapSpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->shouldHaveType('Sylius\Bundle\CoreBundle\Sitemap\Model\Sitemap');
    }

    function it_implements_sitemap_interface()
    {
        $this->shouldImplement(SitemapInterface::class);
    }

    function it_has_sitemap_urls()
    {
        $this->setUrls([]);
        $this->getUrls()->shouldReturn([]);
    }

    function it_adds_url(SitemapUrlInterface $sitemapUrl)
    {
        $this->addUrl($sitemapUrl);
        $this->getUrls()->shouldReturn([$sitemapUrl]);
    }

    function it_removes_url(
        SitemapUrlInterface $sitemapUrl,
        SitemapUrlInterface $productUrl,
        SitemapUrlInterface $staticUrl
    ) {
        $this->addUrl($sitemapUrl);
        $this->addUrl($staticUrl);
        $this->addUrl($productUrl);
        $this->removeUrl($sitemapUrl);

        $this->getUrls()->shouldReturn([1 => $staticUrl, 2 => $productUrl]);
    }

    function it_has_localization()
    {
        $this->setLocalization('http://sylius.org/sitemap1.xml');
        $this->getLocalization()->shouldReturn('http://sylius.org/sitemap1.xml');
    }

    function it_has_last_modification_date(\DateTime $now)
    {
        $this->setLastModification($now);
        $this->getLastModification()->shouldReturn($now);
    }

    function it_throws_sitemap_url_not_found_exception_if_cannot_find_url_to_remove(
        SitemapUrlInterface $productUrl,
        SitemapUrlInterface $staticUrl
    ) {
        $this->addUrl($productUrl);

        $staticUrl->getLocalization()->willReturn('http://sylius.org');

        $this->shouldThrow(SitemapUrlNotFoundException::class)->during('removeUrl', [$staticUrl]);
    }
}
