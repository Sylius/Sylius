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
final class ThemeFilesystemLoader implements \Twig_LoaderInterface, \Twig_ExistsLoaderInterface, \Twig_SourceContextLoaderInterface
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
     *
     * @deprecated To be removed when Twig 1.x compatibility is dropped
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
    public function getSourceContext($name)
    {
        try {
            $path = $this->findTemplate($name);

            return new \Twig_Source(file_get_contents($path), $name, $path);
        } catch (\Exception $exception) {
            // In Twig 2.0, getSourceContext is part of \Twig_LoaderInterface
            if ($this->decoratedLoader instanceof \Twig_SourceContextLoaderInterface || method_exists('\\Twig_LoaderInterface', 'getSourceContext')) {
                return $this->decoratedLoader->getSourceContext($name);
            }

            throw new \Twig_Error_Loader($exception->getMessage(), -1, null, $exception);
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
            // In Twig 2.0, exists is part of \Twig_LoaderInterface
            if ($this->decoratedLoader instanceof \Twig_ExistsLoaderInterface || method_exists('\\Twig_LoaderInterface', 'exists')) {
                return $this->decoratedLoader->exists($name);
            }

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
