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
     * @param EngineInterface $twig
     */
    public function __construct(EngineInterface $twig)
    {
        $this->twig = $twig;
    }

    /**
     * {@inheritdoc}
     */
    public function render($name, array $data = array())
    {
        return $this->twig->renderResponse($name, $data);
    }
}
