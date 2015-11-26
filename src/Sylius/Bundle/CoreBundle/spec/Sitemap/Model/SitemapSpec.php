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
use Doctrine\Common\Collections\Collection;
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
        $this->shouldImplement('Sylius\Bundle\CoreBundle\Sitemap\Model\SitemapInterface');
    }

    function it_is_template_aware()
    {
        $this->shouldImplement('Sylius\Bundle\CoreBundle\Sitemap\Renderer\TemplateAware');
    }

    function it_has_sitemap_url_set(Collection $urlSet)
    {
        $this->setUrlSet($urlSet);
        $this->getUrlSet()->shouldReturn($urlSet);
    }

    function it_adds_url_to_set(Collection $urlSet, SitemapUrlInterface $sitemapUrl)
    {
        $this->setUrlSet($urlSet);
        $urlSet->add($sitemapUrl)->shouldBeCalled();

        $this->addUrl($sitemapUrl);
    }

    function it_removes_url_from_set(Collection $urlSet, SitemapUrlInterface $sitemapUrl)
    {
        $this->setUrlSet($urlSet);
        $urlSet->removeElement($sitemapUrl)->shouldBeCalled();

        $this->removeUrl($sitemapUrl);
    }

    function it_has_loc()
    {
        $this->setLoc('http://sylius.org/sitemap1.xml');
        $this->getLoc()->shouldReturn('http://sylius.org/sitemap1.xml');
    }

    function it_has_lastmod()
    {
        $now = new \DateTime();

        $this->setLastmod($now);
        $this->getLastmod()->shouldReturn($now);
    }

    function it_has_sitemap_template()
    {
        $this->setTemplate('@CoreBundle:Sitemap:sitemap.xml.twig');
        $this->getTemplate()->shouldReturn('@CoreBundle:Sitemap:sitemap.xml.twig');
    }
}
