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

use Sylius\Bundle\CoreBundle\Sitemap\Exception\TemplateNotFoundException;
use Sylius\Bundle\CoreBundle\Sitemap\Model\SitemapInterface;
use Symfony\Component\Templating\EngineInterface;

/**
 * @author Arkadiusz Krakowiak <arkadiusz.krakowiak@lakion.com>
 */
class TwigAdapter implements RendererAdapterInterface
{
    /**
     * @var EngineInterface
     */
    private $twig;

    /**
     * @var array
     */
    private $configuration;

    /**
     * @param EngineInterface $twig
     * @param array           $configuration
     */
    public function __construct(EngineInterface $twig, array $configuration)
    {
        $this->twig = $twig;
        $this->configuration = $configuration;
    }

    /**
     * {@inheritdoc}
     */
    public function render(SitemapInterface $sitemap)
    {
        return $this->twig->render($this->configuration['template'], array('url_set' => $sitemap->getUrls()));
    }
}
