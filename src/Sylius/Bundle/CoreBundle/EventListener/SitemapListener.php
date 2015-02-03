<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\CoreBundle\EventListener;

use Symfony\Component\Routing\RouterInterface;
use Presta\SitemapBundle\Service\SitemapListenerInterface;
use Presta\SitemapBundle\Event\SitemapPopulateEvent;
use Presta\SitemapBundle\Sitemap\Url\UrlConcrete;
use \Sylius\Component\Resource\Repository\RepositoryInterface;

/**
 * Sitemap listener.
 *
 * @author Bartosz Siejka <bartosz.siejka@lakion.com>
 */
class SitemapListener implements SitemapListenerInterface
{
    private $event;
    private $router;
    private $dynamicRouter;
    private $productRepository;
    private $taxonRepository;

    public function __construct(RouterInterface $router, RouterInterface $dynamicRouter, RepositoryInterface $productRepository, RepositoryInterface $taxonRepository)
    {
        $this->router = $router;
        $this->dynamicRouter = $dynamicRouter;
        $this->productRepository = $productRepository;
        $this->taxonRepository = $taxonRepository;
    }

    public function populateSitemap(SitemapPopulateEvent $event)
    {
        $this->event = $event;
        $section = $event->getSection();

        if (is_null($section) || $section == 'default') {
            $homepage = $this->router->generate('sylius_homepage', array(), true);
            $products = $this->productRepository->findAll();
            $taxons = $this->taxonRepository->findAll();
            $statics = $this->dynamicRouter->getRouteCollection()->all();

            $this->createSiteMapEntry($homepage, null, UrlConcrete::CHANGEFREQ_YEARLY, 1);

            foreach ($products as $product) {
                $url = $this->router->generate($product, array(), true);
                $this->createSiteMapEntry($url, $product->getUpdatedAt(), UrlConcrete::CHANGEFREQ_MONTHLY, 0.7);
            }

            foreach ($taxons as $taxon) {
                $url = $this->router->generate($taxon, array(), true);
                $this->createSiteMapEntry($url, $taxon->getUpdatedAt(), UrlConcrete::CHANGEFREQ_MONTHLY, 0.5);
            }

            foreach ($statics as $static) {
                $url = $this->router->generate($static, array(), true);
                $this->createSiteMapEntry($url, null, UrlConcrete::CHANGEFREQ_MONTHLY, 0.3);
            }
        }
    }

    private function createSiteMapEntry($url, $modifiedDate, $changeFrequency, $priority)
    {
        $this->event->getGenerator()->addUrl(
            new UrlConcrete(
                $url,
                $modifiedDate,
                $changeFrequency,
                $priority
            ),
            'default'
        );
    }
}
