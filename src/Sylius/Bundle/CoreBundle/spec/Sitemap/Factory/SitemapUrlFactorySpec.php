<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace spec\Sylius\Bundle\CoreBundle\Sitemap\Factory;

use PhpSpec\ObjectBehavior;
use Sylius\Bundle\CoreBundle\Sitemap\Factory\SitemapUrlFactory;
use Sylius\Bundle\CoreBundle\Sitemap\Factory\SitemapUrlFactoryInterface;
use Sylius\Bundle\CoreBundle\Sitemap\Model\SitemapUrl;
use Sylius\Bundle\CoreBundle\Sitemap\Model\SitemapUrlInterface;

/**
 * @author Arkadiusz Krakowiak <arkadiusz.krakowiak@lakion.com>
 */
final class SitemapUrlFactorySpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->shouldHaveType(SitemapUrlFactory::class);
    }

    function it_implements_sitemap_url_factory_interface()
    {
        $this->shouldImplement(SitemapUrlFactoryInterface::class);
    }

    function it_creates_empty_sitemap_url()
    {
        $this->createNew()->shouldBeLike(new SitemapUrl());
    }
}
