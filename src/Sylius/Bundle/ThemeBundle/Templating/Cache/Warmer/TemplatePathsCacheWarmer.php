<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\ThemeBundle\Templating\Cache\Warmer;

use Sylius\Bundle\ThemeBundle\Context\ThemeContextInterface;
use Sylius\Bundle\ThemeBundle\Repository\ThemeRepositoryInterface;
use Symfony\Bundle\FrameworkBundle\CacheWarmer\TemplateFinderInterface;
use Symfony\Component\Config\FileLocatorInterface;
use Symfony\Component\HttpKernel\CacheWarmer\CacheWarmer;
use Symfony\Component\Templating\TemplateReferenceInterface;

/**
 * @author Kamil Kokot <kamil.kokot@lakion.com>
 */
class TemplatePathsCacheWarmer extends CacheWarmer
{
    /**
     * @var TemplateFinderInterface
     */
    private $finder;

    /**
     * @var FileLocatorInterface
     */
    private $locator;

    /**
     * @var ThemeRepositoryInterface
     */
    private $themeRepository;

    /**
     * @var ThemeContextInterface
     */
    private $themeContext;

    /**
     * @param TemplateFinderInterface $finder
     * @param FileLocatorInterface $locator
     * @param ThemeRepositoryInterface $themeRepository
     * @param ThemeContextInterface $themeContext
     */
    public function __construct(
        TemplateFinderInterface $finder,
        FileLocatorInterface $locator,
        ThemeRepositoryInterface $themeRepository,
        ThemeContextInterface $themeContext
    ) {
        $this->finder = $finder;
        $this->locator = $locator;
        $this->themeRepository = $themeRepository;
        $this->themeContext = $themeContext;
    }

    /**
     * {@inheritdoc}
     */
    public function warmUp($cacheDir)
    {
        $templates = [];

        $themes = $this->themeRepository->findAll();

        /** @var TemplateReferenceInterface $template */
        foreach ($this->finder->findAllTemplates() as $template) {
            $this->themeContext->clear();
            
            $templates[$template->getLogicalName()] = $this->locator->locate($template);

            foreach ($themes as $theme) {
                $this->themeContext->setTheme($theme);

                $path = $this->locator->locate($template);

                if ($path !== $templates[$template->getLogicalName()]) {
                    $templates[$template->getLogicalName() . "|" . $theme->getLogicalName()] = $path;
                }
            }
        }

        $this->writeCacheFile($cacheDir . '/templates.php', sprintf('<?php return %s;', var_export($templates, true)));
    }

    /**
     * {@inheritdoc}
     */
    public function isOptional()
    {
        return true;
    }
}