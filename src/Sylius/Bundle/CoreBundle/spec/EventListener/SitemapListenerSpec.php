<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace spec\Sylius\Bundle\CoreBundle\EventListener;

use PhpSpec\ObjectBehavior;
use Sylius\Component\Resource\Repository\RepositoryInterface;
use Symfony\Component\Routing\RouterInterface;
use Presta\SitemapBundle\Service\SitemapListenerInterface;
use Presta\SitemapBundle\Event\SitemapPopulateEvent;
use Presta\SitemapBundle\Sitemap\Url\UrlConcrete;
use Presta\SitemapBundle\Service\AbstractGenerator;

/**
 * @author Bartosz Siejka <siejka.bartosz@gmail.com>
 */
class SitemapListenerSpec extends ObjectBehavior
{
    function let(RouterInterface $router, RouterInterface $dynamicRouter, RepositoryInterface $productRepository, RepositoryInterface $taxonRepository)
    {
        $this->beConstructedWith($router, $dynamicRouter, $productRepository, $taxonRepository);
    }
    
    function it_is_initializable()
    {
        $this->shouldHaveType('Sylius\Bundle\CoreBundle\EventListener\SitemapListener');
    }
    
    function it_implements_sitemap_interface()
    {
        $this->shouldImplement('Presta\SitemapBundle\Service\SitemapListenerInterface');
    }
}
