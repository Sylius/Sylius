<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Sylius\Bundle\ThemeBundle\Twig;

use Symfony\Component\Config\FileLocatorInterface;
use Symfony\Component\Templating\TemplateNameParserInterface;
use Symfony\Component\Templating\TemplateReferenceInterface;

/**
 * @author Kamil Kokot <kamil@kokot.me>
 */
final class ThemeFilesystemLoader implements \Twig_LoaderInterface
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
    public function getSourceContext($name)
    {
        try {
            $path = $this->findTemplate($name);

            return new \Twig_Source(file_get_contents($path), $name, $path);
        } catch (\Exception $exception) {
            return $this->decoratedLoader->getSourceContext($name);
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
            return $this->decoratedLoader->exists($name);
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
