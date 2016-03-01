<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
 
namespace spec\Sylius\Bundle\CoreBundle\Sitemap\Exception;
 
use PhpSpec\ObjectBehavior;
use Sylius\Bundle\CoreBundle\Sitemap\Model\SitemapUrlInterface;

/**
 * @author Arkadiusz Krakowiak <arkadiusz.krakowiak@lakion.com>
 */
class SitemapUrlNotFoundExceptionSpec extends ObjectBehavior
{
    function let(SitemapUrlInterface $sitemapUrl)
    {
        $sitemapUrl->getLocalization()->willReturn('http://sylius.org');
        $this->beConstructedWith($sitemapUrl, null);
    }

    function it_is_an_exception()
    {
        $this->shouldHaveType(\Exception::class);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType('Sylius\Bundle\CoreBundle\Sitemap\Exception\SitemapUrlNotFoundException');
    }
}
