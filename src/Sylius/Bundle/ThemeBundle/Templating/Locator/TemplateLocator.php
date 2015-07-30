<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\ThemeBundle\Templating\Locator;

use Sylius\Bundle\ThemeBundle\Context\ThemeContextInterface;
use Sylius\Bundle\ThemeBundle\Locator\ResourceLocatorInterface;
use Sylius\Bundle\ThemeBundle\Model\ThemeInterface;
use Sylius\Bundle\ThemeBundle\Repository\ThemeRepositoryInterface;
use Symfony\Component\Config\FileLocatorInterface;
use Symfony\Component\HttpKernel\KernelInterface;
use Symfony\Component\Templating\TemplateReferenceInterface;

/**
 * {@inheritdoc}
 *
 * @author Kamil Kokot <kamil.kokot@lakion.com>
 */
class TemplateLocator implements FileLocatorInterface
{
    /**
     * @var ThemeRepositoryInterface
     */
    private $themeRepository;

    /**
     * @var ThemeContextInterface
     */
    private $themeContext;

    /**
     * @var ResourceLocatorInterface
     */
    private $bundleResourceLocator;

    /**
     * @var ResourceLocatorInterface
     */
    private $applicationResourceLocator;

    /**
     * @var array
     */
    private $cache;

    /**
     * @param ThemeRepositoryInterface $themeRepository
     * @param ThemeContextInterface $themeContext
     * @param ResourceLocatorInterface $bundleResourceLocator
     * @param ResourceLocatorInterface $applicationResourceLocator
     * @param string $cacheDir The cache path
     */
    public function __construct(
        ThemeRepositoryInterface $themeRepository,
        ThemeContextInterface $themeContext,
        ResourceLocatorInterface $bundleResourceLocator,
        ResourceLocatorInterface $applicationResourceLocator,
        $cacheDir = null
    ) {
        $this->themeRepository = $themeRepository;
        $this->themeContext = $themeContext;
        $this->bundleResourceLocator = $bundleResourceLocator;
        $this->applicationResourceLocator = $applicationResourceLocator;

        if (null !== $cacheDir && is_file($cache = $cacheDir . '/templates.php')) {
            $this->cache = require $cache;
        }
    }

    /**
     * Returns a full path for a given file.
     *
     * @param TemplateReferenceInterface $template A template
     * @param string $currentPath Unused
     * @param bool $first Unused
     *
     * @return string The full path for the file
     *
     * @throws \InvalidArgumentException When the template is not an instance of TemplateReferenceInterface
     * @throws \InvalidArgumentException When the template file can not be found
     */
    public function locate($template, $currentPath = null, $first = true)
    {
        if (!$template instanceof TemplateReferenceInterface) {
            throw new \InvalidArgumentException("The template must be an instance of TemplateReferenceInterface.");
        }

        $themes = $this->themeContext->getThemes();

        $templatePath = $this->locateTemplateUsingThemes($template, $themes);

        if (null === $templatePath) {
            throw new \InvalidArgumentException(
                sprintf('Unable to find template "%s" (using themes: %s).', $template, join(', ', $themes))
            );
        }

        return $templatePath;
    }

    /**
     * @param TemplateReferenceInterface $template
     * @param ThemeInterface[] $themes
     *
     * @return null|string
     */
    protected function locateTemplateUsingThemes(TemplateReferenceInterface $template, array $themes)
    {
        foreach (array_merge($themes, [null]) as $theme) {
            $result = $this->getCache($template, $theme);
            if (null !== $result) {
                return $result;
            }
        }

        if (0 === strpos($template->getPath(), '@')) {
            return $this->locateBundleTemplateUsingThemes($template, $themes);
        }

        return $this->locateAppTemplateUsingThemes($template, $themes);
    }

    /**
     * @param TemplateReferenceInterface $template
     * @param ThemeInterface[] $themes
     *
     * @return null|string
     */
    protected function locateBundleTemplateUsingThemes(TemplateReferenceInterface $template, array $themes)
    {
        $cacheKey = $this->getCacheKey($template, $this->themeContext->getTheme());

        return $this->cache[$cacheKey] = $this->bundleResourceLocator->locateResource($template->getPath(), $themes);
    }

    /**
     * @param TemplateReferenceInterface $template
     * @param ThemeInterface[] $themes
     *
     * @return null|string
     */
    protected function locateAppTemplateUsingThemes(TemplateReferenceInterface $template, array $themes = [])
    {
        $cacheKey = $this->getCacheKey($template, $this->themeContext->getTheme());

        return $this->cache[$cacheKey] = $this->applicationResourceLocator->locateResource($template->getPath(), $themes);
    }

    /**
     * @param TemplateReferenceInterface $template
     * @param ThemeInterface|null $theme
     *
     * @return string
     */
    private function getCacheKey(TemplateReferenceInterface $template, ThemeInterface $theme = null)
    {
        $key = $template->getLogicalName();

        if (null !== $theme) {
            $key .= '|' . $theme->getLogicalName();
        }

        return $key;
    }

    /**
     * @param TemplateReferenceInterface $template
     * @param ThemeInterface|null $theme
     *
     * @return null
     */
    private function getCache(TemplateReferenceInterface $template, ThemeInterface $theme = null)
    {
        $key = $this->getCacheKey($template, $theme);

        return isset($this->cache[$key]) ? $this->cache[$key] : null;
    }
}