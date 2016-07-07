<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
 
namespace Sylius\Bundle\CoreBundle\Sitemap\Provider;

use Sylius\Bundle\CoreBundle\Sitemap\Factory\SitemapUrlFactoryInterface;
use Sylius\Bundle\CoreBundle\Sitemap\Model\ChangeFrequency;
use Sylius\Component\Resource\Repository\RepositoryInterface;
use Symfony\Component\Routing\RouterInterface;

/**
 * @author Arkadiusz Krakowiak <arkadiusz.krakowiak@lakion.com>
 */
class ProductUrlProvider implements UrlProviderInterface
{
    /**
     * @var RepositoryInterface
     */
    private $productRepository;

    /**
     * @var RouterInterface
     */
    private $router;

    /**
     * @var SitemapUrlFactoryInterface
     */
    private $sitemapUrlFactory;

    /**
     * @var array
     */
    private $urls = [];

    /**
     * @param RepositoryInterface $productRepository
     * @param RouterInterface $router
     * @param SitemapUrlFactoryInterface $sitemapUrlFactory
     */
    public function __construct(
        RepositoryInterface $productRepository,
        RouterInterface $router,
        SitemapUrlFactoryInterface $sitemapUrlFactory
    ) {
        $this->productRepository = $productRepository;
        $this->router = $router;
        $this->sitemapUrlFactory = $sitemapUrlFactory;
    }

    /**
     * {@inheritdoc}
     */
    public function generate()
    {
        $products = $this->productRepository->findAll();

        foreach ($products as $product) {
            $productUrl = $this->sitemapUrlFactory->createNew();
            $localization = $this->router->generate($product, [], true);

            $productUrl->setLastModification($product->getUpdatedAt());
            $productUrl->setLocalization($localization);
            $productUrl->setChangeFrequency(ChangeFrequency::always());
            $productUrl->setPriority(0.5);

            $this->urls[] = $productUrl;
        }

        return $this->urls;
    }
}
