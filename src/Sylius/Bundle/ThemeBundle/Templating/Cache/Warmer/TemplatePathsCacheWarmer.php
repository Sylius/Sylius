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

use Sylius\Bundle\ThemeBundle\Locator\ResourceLocatorInterface;
use Sylius\Bundle\ThemeBundle\Model\ThemeInterface;
use Sylius\Bundle\ThemeBundle\Repository\ThemeRepositoryInterface;
use Symfony\Bundle\FrameworkBundle\CacheWarmer\TemplateFinderInterface;
use Symfony\Component\Config\FileLocatorInterface;
use Symfony\Component\HttpKernel\CacheWarmer\CacheWarmer;
use Symfony\Component\Templating\TemplateReferenceInterface;

/**
 * @author Kamil Kokot <kamil.kokot@lakion.com>
 */
final class TemplatePathsCacheWarmer extends CacheWarmer
{
    /**
     * @var TemplateFinderInterface
     */
    private $templateFinder;

    /**
     * @var ResourceLocatorInterface
     */
    private $resourceLocator;

    /**
     * @var ThemeRepositoryInterface
     */
    private $themeRepository;

    /**
     * @param TemplateFinderInterface $templateFinder
     * @param ResourceLocatorInterface $resourceLocator
     * @param ThemeRepositoryInterface $themeRepository
     */
    public function __construct(
        TemplateFinderInterface $templateFinder,
        ResourceLocatorInterface $resourceLocator,
        ThemeRepositoryInterface $themeRepository
    ) {
        $this->templateFinder = $templateFinder;
        $this->resourceLocator = $resourceLocator;
        $this->themeRepository = $themeRepository;
    }

    /**
     * {@inheritdoc}
     */
    public function warmUp($cacheDir)
    {
        $templates = $this->templateFinder->findAllTemplates();

        $templatesLocations = [];

        /** @var ThemeInterface $theme */
        foreach ($this->themeRepository->findAll() as $theme) {
            /** @var TemplateReferenceInterface $template */
            foreach ($templates as $template) {

                $templatesLocations[$template->getLogicalName() . "|" . $theme->getSlug()] = $this->resourceLocator->locateResource($template->getPath(), $theme);
            }
        }

        $this->writeCacheFile($cacheDir . '/templates_themes.php', sprintf('<?php return %s;', var_export($templatesLocations, true)));
    }

    /**
     * {@inheritdoc}
     */
    public function isOptional()
    {
        return true;
    }
}
