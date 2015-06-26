<?php

namespace Sylius\Bundle\ThemeBundle\Templating\Locator;

use Sylius\Bundle\ThemeBundle\Context\ThemeContextInterface;
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
     * @var KernelInterface
     */
    private $kernel;

    /**
     * @var ThemeRepositoryInterface
     */
    private $themeRepository;

    /**
     * @var ThemeContextInterface
     */
    private $themeContext;

    /**
     * @var string
     */
    private $appDir;

    /**
     * @var array
     */
    private $cache;

    /**
     * @var array
     */
    private $paths = [
        'bundle_template' => [
            '%theme_path%/%bundle_name%/%override_path%',
            '%app_path%/Resources/%bundle_name%/%override_path%',
            '%bundle_path%/Resources/%override_path%',
        ],
        'app_template' => [
            '%theme_path%/%override_path%',
            '%app_path%/Resources/%override_path%',
        ],
    ];

    /**
     * @param KernelInterface $kernel
     * @param ThemeRepositoryInterface $themeRepository
     * @param ThemeContextInterface $themeContext
     * @param string $appDir
     * @param string $cacheDir The cache path
     */
    public function __construct(
        KernelInterface $kernel,
        ThemeRepositoryInterface $themeRepository,
        ThemeContextInterface $themeContext,
        $appDir,
        $cacheDir = null
    ) {
        $this->kernel = $kernel;
        $this->themeRepository = $themeRepository;
        $this->themeContext = $themeContext;
        $this->appDir = $appDir;

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

        $themes = $this->themeContext->getThemesSortedByPriorityInDescendingOrder();

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
        } else {
            return $this->locateAppTemplateUsingThemes($template, $themes);
        }
    }

    /**
     * @param TemplateReferenceInterface $template
     * @param ThemeInterface[] $themes
     *
     * @return null|string
     */
    protected function locateBundleTemplateUsingThemes(TemplateReferenceInterface $template, array $themes)
    {
        $name = $template->getPath();

        if (false !== strpos($name, '..')) {
            throw new \RuntimeException(sprintf('File name "%s" contains invalid characters (..).', $name));
        }

        $bundleName = substr($name, 1);
        $templatePath = '';
        if (false !== strpos($bundleName, '/')) {
            list($bundleName, $templatePath) = explode('/', $bundleName, 2);
        }
        if (0 !== strpos($templatePath, 'Resources')) {
            throw new \RuntimeException('Template files have to be in Resources.');
        }

        $resourceBundle = null;
        $bundles = $this->kernel->getBundle($bundleName, false);

        $parameters = [
            '%app_path%' => $this->appDir,
            '%override_path%' => substr($templatePath, strlen('Resources/')),
        ];

        foreach ($this->paths['bundle_template'] as $path) {
            foreach ($bundles as $bundle) {
                $parameters = array_merge($parameters, [
                    '%bundle_name%' => $bundle->getName(),
                    '%bundle_path%' => $bundle->getPath(),
                ]);

                if (false === strpos($path, '%theme_path%')) {
                    if (null !== $checkedPath = $this->checkPath($path, $parameters, $template)) {
                        return $checkedPath;
                    }
                } else {
                    foreach ($themes as $theme) {
                        $themeParameters = array_merge($parameters, [
                            '%theme_path%' => $theme->getPath(),
                        ]);

                        if (null !== $checkedPath = $this->checkPath($path, $themeParameters, $template, $theme)) {
                            return $checkedPath;
                        }
                    }
                }
            }
        }

        return null;
    }

    /**
     * @param TemplateReferenceInterface $template
     * @param ThemeInterface[] $themes
     *
     * @return null|string
     */
    protected function locateAppTemplateUsingThemes(TemplateReferenceInterface $template, array $themes = [])
    {
        $parameters = [
            '%app_path%' => $this->appDir,
            '%override_path%' => $template->getPath(),
        ];

        foreach ($this->paths['app_template'] as $path) {
            if (false === strpos($path, '%theme_path%')) {
                if (null !== $checkedPath = $this->checkPath($path, $parameters, $template)) {
                    return $checkedPath;
                }
            } else {
                foreach ($themes as $theme) {
                    $themeParameters = array_merge($parameters, [
                        '%theme_path%' => $theme->getPath(),
                    ]);

                    if (null !== $checkedPath = $this->checkPath($path, $themeParameters, $template, $theme)) {
                        return $checkedPath;
                    }
                }
            }
        }

        return null;
    }

    /**
     * @param string $path
     * @param array $parameters
     * @param TemplateReferenceInterface $template
     * @param ThemeInterface $theme
     * @return null|string
     */
    protected function checkPath($path, $parameters, TemplateReferenceInterface $template, ThemeInterface $theme = null)
    {
        $key = $this->getCacheKey($template, $theme);

        if (isset($this->cache[$key])) {
            return $this->cache[$key];
        }

        $path = strtr($path, $parameters);

        if (file_exists($path)) {
            return $this->cache[$key] = $path;
        }

        return null;
    }

    private function getCacheKey(TemplateReferenceInterface $template, ThemeInterface $theme = null)
    {
        $key = $template->getLogicalName();

        if (null !== $theme) {
            $key .= '|' . $theme->getLogicalName();
        }

        return $key;
    }

    private function getCache(TemplateReferenceInterface $template, ThemeInterface $theme = null)
    {
        $key = $this->getCacheKey($template, $theme);

        return isset($this->cache[$key]) ? $this->cache[$key] : null;
    }
}