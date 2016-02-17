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

use Doctrine\Common\Cache\Cache;
use Sylius\Bundle\ThemeBundle\Locator\ResourceNotFoundException;
use Sylius\Bundle\ThemeBundle\Model\ThemeInterface;
use Symfony\Component\Templating\TemplateReferenceInterface;

/**
 * @author Kamil Kokot <kamil.kokot@lakion.com>
 */
final class CachedTemplateLocator implements TemplateLocatorInterface
{
    /**
     * @var TemplateLocatorInterface
     */
    private $decoratedTemplateLocator;

    /**
     * @var Cache
     */
    private $cache;

    /**
     * @param TemplateLocatorInterface $decoratedTemplateLocator
     * @param Cache $cache
     */
    public function __construct(TemplateLocatorInterface $decoratedTemplateLocator, Cache $cache)
    {
        $this->decoratedTemplateLocator = $decoratedTemplateLocator;
        $this->cache = $cache;
    }

    /**
     * {@inheritdoc}
     */
    public function locateTemplate(TemplateReferenceInterface $template, ThemeInterface $theme)
    {
        $cacheKey = $this->getCacheKey($template, $theme);
        if ($this->cache->contains($cacheKey)) {
            $location = $this->cache->fetch($cacheKey);

            if (null === $location) {
                throw new ResourceNotFoundException($template->getPath(), $theme);
            }

            return $location;
        }

        return $this->decoratedTemplateLocator->locateTemplate($template, $theme);
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
