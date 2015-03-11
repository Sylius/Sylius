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
use Sylius\Component\Resource\Repository\RepositoryInterface;

/**
 * Sitemap listener.
 *
 * @author Bartosz Siejka <siejka.bartosz@gmail.com>
 */
class SitemapListener implements SitemapListenerInterface
{
    private $event;
    private $section;
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

    /**
     *{@inheritdoc}
     */
    public function populateSitemap(SitemapPopulateEvent $event)
    {
        $this->event = $event;
        $this->section = $event->getSection();
        
        $this->homepageSitemap();
        $this->productSitemap();
        $this->taxonSitemap();
        $this->staticSitemap();
    }
    
    /**
     * Create part of sitemap with urls to static pages.
     * 
     * @return void
     */
    protected function staticSitemap()
    {
         if (null === $this->section || 'default' === $this->section) {
            $statics = $this->dynamicRouter->getRouteCollection()->all();

            foreach ($statics as $static) {
                $url = $this->router->generate($static, array(), true);
                $this->createSiteMapEntry($url, null, UrlConcrete::CHANGEFREQ_MONTHLY, 0.3);
            }
        }
    }

    /**
     * Create part of sitemap with urls to taxonomies.
     * 
     * @return void
     */
    protected function taxonSitemap()
    {
        if (null === $this->section || 'default' === $this->section) {
            $taxons = $this->taxonRepository->findAll();

            foreach ($taxons as $taxon) {
                $url = $this->router->generate($taxon, array(), true);
                $this->createSiteMapEntry($url, $taxon->getUpdatedAt(), UrlConcrete::CHANGEFREQ_MONTHLY, 0.5);
            }
        }
    }
    
    /**
     * Create part of sitemap with urls to products.
     * 
     * @return void
     */
    protected function productSitemap() 
    {
        if (null === $this->section || 'default' === $this->section) {
            $products = $this->productRepository->findAll();

            foreach ($products as $product) {
                $url = $this->router->generate($product, array(), true);
                $this->createSiteMapEntry($url, $product->getUpdatedAt(), UrlConcrete::CHANGEFREQ_MONTHLY, 0.7);
            }
        }
    }

    /**
     * @param type $url
     * @param type $modifiedDate
     * @param type $changeFrequency
     * @param type $priority
     * @return void
     */
    protected function createSiteMapEntry($url, $modifiedDate, $changeFrequency, $priority)
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
    
    /**
     * Create part of sitemap with url to homepage
     */
    private function homepageSitemap() {
        if (null === $this->section || 'default' === $this->section) {
            $homepage = $this->router->generate('sylius_homepage', array(), true);

            $this->createSiteMapEntry($homepage, null, UrlConcrete::CHANGEFREQ_YEARLY, 1);
        }
    }
}
