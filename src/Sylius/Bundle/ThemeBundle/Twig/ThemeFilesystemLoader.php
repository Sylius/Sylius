<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\ThemeBundle\Twig;

use Symfony\Component\Config\FileLocatorInterface;
use Symfony\Component\Templating\TemplateNameParserInterface;
use Symfony\Component\Templating\TemplateReferenceInterface;

/**
 * @author Kamil Kokot <kamil.kokot@lakion.com>
 */
final class ThemeFilesystemLoader implements \Twig_LoaderInterface, \Twig_ExistsLoaderInterface
{
    /**
     * @var \Twig_LoaderInterface
     */
    private $decoratedLoader;

    /**
     * @var FileLocatorInterface
     */
    private $templateLocator;

    /**
     * @var TemplateNameParserInterface
     */
    private $templateNameParser;

    /**
     * @var array
     */
    private $cache = [];

    /**
     * @param \Twig_LoaderInterface $decoratedLoader
     * @param FileLocatorInterface $templateLocator
     * @param TemplateNameParserInterface $templateNameParser
     */
    public function __construct(
        \Twig_LoaderInterface $decoratedLoader,
        FileLocatorInterface $templateLocator,
        TemplateNameParserInterface $templateNameParser
    ) {
        $this->decoratedLoader = $decoratedLoader;
        $this->templateLocator = $templateLocator;
        $this->templateNameParser = $templateNameParser;
    }

    /**
     * {@inheritdoc}
     */
    public function getSource($name)
    {
        try {
            return file_get_contents($this->findTemplate($name));
        } catch (\Exception $exception) {
            return $this->decoratedLoader->getSource($name);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function getCacheKey($name)
    {
        try {
            return $this->findTemplate($name);
        } catch (\Exception $exception) {
            return $this->decoratedLoader->getCacheKey($name);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function isFresh($name, $time)
    {
        try {
            return filemtime($this->findTemplate($name)) <= $time;
        } catch (\Exception $exception) {
            return $this->decoratedLoader->isFresh($name, $time);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function exists($name)
    {
        try {
            return stat($this->findTemplate($name)) !== false;
        } catch (\Exception $exception) {
            return false;
        }
    }

    /**
     * @param TemplateReferenceInterface|string $template
     *
     * @return string
     */
    private function findTemplate($template)
    {
        $logicalName = (string) $template;

        if (isset($this->cache[$logicalName])) {
            return $this->cache[$logicalName];
        }

        $template = $this->templateNameParser->parse($template);
        $file = $this->templateLocator->locate($template);

        return $this->cache[$logicalName] = $file;
    }
}
