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
final class TemplatePathsCacheWarmer extends CacheWarmer
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
     * @param TemplateFinderInterface $finder
     * @param FileLocatorInterface $locator
     */
    public function __construct(
        TemplateFinderInterface $finder,
        FileLocatorInterface $locator
    ) {
        $this->finder = $finder;
        $this->locator = $locator;
    }

    /**
     * {@inheritdoc}
     */
    public function warmUp($cacheDir)
    {
        $templates = [];

        /** @var TemplateReferenceInterface $template */
        foreach ($this->finder->findAllTemplates() as $template) {
            $templates[$template->getLogicalName()] = $this->locator->locate($template);
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
