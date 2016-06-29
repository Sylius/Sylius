<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
 
namespace Sylius\CoreBundle\Sitemap\Builder;

use Sylius\CoreBundle\Sitemap\Factory\SitemapFactoryInterface;
use Sylius\CoreBundle\Sitemap\Provider\UrlProviderInterface;
use Symfony\Component\DependencyInjection\ContainerAware;

/**
 * @author Arkadiusz Krakowiak <arkadiusz.krakowiak@lakion.com>
 */
class SitemapBuilder implements SitemapBuilderInterface
{
    /**
     * @var SitemapFactoryInterface
     */
    private $sitemapFactory;

    /**
     * @var array
     */
    private $providers = [];

    /**
     * @param SitemapFactoryInterface $sitemapFactory
     */
    public function __construct(SitemapFactoryInterface $sitemapFactory)
    {
        $this->sitemapFactory = $sitemapFactory;
    }

    /**
     * {@inheritdoc}
     */
    public function addProvider(UrlProviderInterface $provider)
    {
        $this->providers[] = $provider;
    }

    /**
     * {@inheritdoc}
     */
    public function build()
    {
        $sitemap = $this->sitemapFactory->createNew();
        $urls = [];

        foreach ($this->providers as $provider) {
            $urls = array_merge($urls, $provider->generate());
        }
        $sitemap->setUrls($urls);

        return $sitemap;
    }
}
