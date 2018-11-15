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

namespace Sylius\Bundle\ThemeBundle\Templating\Locator;

use Doctrine\Common\Cache\Cache;
use Sylius\Bundle\ThemeBundle\Locator\ResourceNotFoundException;
use Sylius\Bundle\ThemeBundle\Model\ThemeInterface;
use Symfony\Component\Templating\TemplateReferenceInterface;

final class CachedTemplateLocator implements TemplateLocatorInterface
{
    /** @var TemplateLocatorInterface */
    private $decoratedTemplateLocator;

    /** @var Cache */
    private $cache;

    public function __construct(TemplateLocatorInterface $decoratedTemplateLocator, Cache $cache)
    {
        $this->decoratedTemplateLocator = $decoratedTemplateLocator;
        $this->cache = $cache;
    }

    /**
     * {@inheritdoc}
     */
    public function locateTemplate(TemplateReferenceInterface $template, ThemeInterface $theme): string
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

    private function getCacheKey(TemplateReferenceInterface $template, ThemeInterface $theme): string
    {
        return $template->getLogicalName() . '|' . $theme->getName();
    }
}
