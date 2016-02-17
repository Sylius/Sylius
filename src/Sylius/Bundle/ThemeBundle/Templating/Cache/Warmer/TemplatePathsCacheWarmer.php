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

use Doctrine\Common\Cache\Cache;
use Sylius\Bundle\ThemeBundle\Locator\ResourceNotFoundException;
use Sylius\Bundle\ThemeBundle\Model\ThemeInterface;
use Sylius\Bundle\ThemeBundle\Repository\ThemeRepositoryInterface;
use Sylius\Bundle\ThemeBundle\Templating\Locator\TemplateLocatorInterface;
use Symfony\Bundle\FrameworkBundle\CacheWarmer\TemplateFinderInterface;
use Symfony\Component\HttpKernel\CacheWarmer\CacheWarmerInterface;
use Symfony\Component\Templating\TemplateReferenceInterface;

/**
 * @author Kamil Kokot <kamil.kokot@lakion.com>
 */
final class TemplatePathsCacheWarmer implements CacheWarmerInterface
{
    /**
     * @var TemplateFinderInterface
     */
    private $templateFinder;

    /**
     * @var TemplateLocatorInterface
     */
    private $templateLocator;

    /**
     * @var ThemeRepositoryInterface
     */
    private $themeRepository;

    /**
     * @var Cache
     */
    private $cache;

    /**
     * @param TemplateFinderInterface $templateFinder
     * @param TemplateLocatorInterface $templateLocator
     * @param ThemeRepositoryInterface $themeRepository
     * @param Cache $cache
     */
    public function __construct(
        TemplateFinderInterface $templateFinder,
        TemplateLocatorInterface $templateLocator,
        ThemeRepositoryInterface $themeRepository,
        Cache $cache
    ) {
        $this->templateFinder = $templateFinder;
        $this->templateLocator = $templateLocator;
        $this->themeRepository = $themeRepository;
        $this->cache = $cache;
    }

    /**
     * {@inheritdoc}
     */
    public function warmUp($cacheDir)
    {
        $templates = $this->templateFinder->findAllTemplates();

        /** @var TemplateReferenceInterface $template */
        foreach ($templates as $template) {
            $this->warmUpTemplate($template);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function isOptional()
    {
        return true;
    }

    /**
     * @param TemplateReferenceInterface $template
     */
    private function warmUpTemplate(TemplateReferenceInterface $template)
    {
        /** @var ThemeInterface $theme */
        foreach ($this->themeRepository->findAll() as $theme) {
            $this->warmUpThemeTemplate($template, $theme);
        }
    }

    /**
     * @param TemplateReferenceInterface $template
     * @param ThemeInterface $theme
     */
    private function warmUpThemeTemplate(TemplateReferenceInterface $template, ThemeInterface $theme)
    {
        try {
            $location = $this->templateLocator->locateTemplate($template, $theme);
        } catch (ResourceNotFoundException $exception) {
            $location = null;
        }

        $this->cache->save($this->getCacheKey($template, $theme), $location);
    }

    /**
     * @param TemplateReferenceInterface $template
     * @param ThemeInterface $theme
     *
     * @return string
     */
    private function getCacheKey(TemplateReferenceInterface $template, ThemeInterface $theme)
    {
        return $template->getLogicalName().'|'.$theme->getName();
    }
}
