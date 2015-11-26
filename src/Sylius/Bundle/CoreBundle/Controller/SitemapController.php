<?php

/*
 * This file is part of the Lakion package.
 *
 * (c) Lakion
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\CoreBundle\Controller;

use Sylius\Bundle\CoreBundle\Sitemap\Model\Sitemap;
use Sylius\Bundle\CoreBundle\Sitemap\Model\SitemapUrl;
use Sylius\Bundle\CoreBundle\Sitemap\Model\SitemapUrlInterface;
use Sylius\Bundle\CoreBundle\Sitemap\Renderer\SitemapRendererInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\Request;

/**
 * [WIP]
 * @author Arkadiusz Krakowiak <arkadiusz.krakowiak@lakion.com>
 */
class SitemapController
{
    /**
     * @var array
     */
    private $configuration;

    /**
     * @var ContainerInterface
     */
    private $container;

    private $sitemapRenderer;

    public function __construct(array $configuration = array(), ContainerInterface $container, SitemapRendererInterface $sitemapRenderer)
    {
        $this->configuration = $configuration;
        $this->container = $container;
        $this->sitemapRenderer = $sitemapRenderer;
    }

    public function indexAction(Request $request)
    {
        $urls = array();

        $hostname = $request->getHost();
        $channel = $this->get('sylius.repository.channel')->findOneBy(array('url' => 'localhost'));
        $router = $this->get('router');

        $urls[] = array('loc' => $router->generate('sylius_homepage', array(), true), 'changefreq' => 'weekly', 'priority' => '0.7');

        foreach ($this->configuration['resources'] as $resourceName) {
            $resources = $this->getResourceRepository($resourceName)->findBy(array('channels' => $channel));
            foreach ($resources as $resource) {
                $urls[] = array('loc' => $router->generate($resource, array(), true), 'changefreq' => 'weekly', 'priority' => '0.5');
            }
        }

        return $this->get('templating')->renderResponse('@SyliusCore/Sitemap/index.xml.twig', array('urls' => $urls, 'hostname' => $hostname));
    }

    public function showAction()
    {
        $sitemap = new Sitemap();
        $sitemap->setTemplate($this->configuration['template']);
        $router = $this->get('router');

        $products = $this->getResourceRepository('product')->findAll();

        foreach ($products as $product) {
            $sitemapUrl = new SitemapUrl();
            $loc = $router->generate($product, array(), true);
            $sitemapUrl->setLoc($loc);
            $sitemapUrl->setChangefreq(SitemapUrlInterface::CHANGEFREQ_ALWAYS);
            $sitemapUrl->setPriority(0.5);

            $sitemap->addUrl($sitemapUrl);
        }

        return $this->sitemapRenderer->render($sitemap);
    }

    private function get($id)
    {
        return $this->container->get($id);
    }

    private function getResourceRepository($resourceName)
    {
        return $this->get('sylius.repository.'.$resourceName);
    }
}
