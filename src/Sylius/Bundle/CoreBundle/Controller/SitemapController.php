<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\CoreBundle\Controller;

use Sylius\Bundle\CoreBundle\Sitemap\Renderer\SitemapRendererInterface;
use Sylius\Bundle\CoreBundle\Sitemap\Builder\SitemapBuilderInterface;
use Symfony\Component\HttpFoundation\Response;

/**
 * @author Arkadiusz Krakowiak <arkadiusz.krakowiak@lakion.com>
 */
class SitemapController
{
    /**
     * @var SitemapRendererInterface
     */
    private $sitemapRenderer;

    /**
     * @var SitemapBuilderInterface
     */
    private $sitemapBuilder;

    /**
     * @param SitemapRendererInterface $sitemapRenderer
     * @param SitemapBuilderInterface $sitemapBuilder
     */
    public function __construct(SitemapRendererInterface $sitemapRenderer, SitemapBuilderInterface $sitemapBuilder)
    {
        $this->sitemapRenderer = $sitemapRenderer;
        $this->sitemapBuilder = $sitemapBuilder;
    }

    /**
     * @return Response
     */
    public function showAction()
    {
        $sitemap = $this->sitemapBuilder->build();

        $response = new Response($this->sitemapRenderer->render($sitemap));
        $response->headers->set('Content-Type', 'application/xml');

        return $response;
    }
}
