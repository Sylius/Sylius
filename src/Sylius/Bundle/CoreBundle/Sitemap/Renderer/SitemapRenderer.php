<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
 
namespace Sylius\Bundle\CoreBundle\Sitemap\Renderer;

use Sylius\Bundle\CoreBundle\Sitemap\Model\SitemapInterface;

/**
 * @author Arkadiusz Krakowiak <arkadiusz.krakowiak@lakion.com>
 */
class SitemapRenderer implements SitemapRendererInterface
{
    /**
     * @var RendererAdapterInterface
     */
    private $adapter;

    /**
     * {@inheritdoc}
     */
    public function __construct(RendererAdapterInterface $adapter)
    {
        $this->adapter = $adapter;
    }

    public function render(SitemapInterface $sitemap)
    {
        $template = $sitemap->getTemplate();
        $urlSet = $sitemap->getUrlSet();

        $data = array(
            'url_set' => $urlSet
        );

        return $this->adapter->render($template, $data);
    }

    /**
     * {@inheritdoc}
     */
    public function getAdapter()
    {
        return $this->adapter;
    }

    /**
     * {@inheritdoc}
     */
    public function setAdapter($adapter)
    {
        $this->adapter = $adapter;
    }
}
