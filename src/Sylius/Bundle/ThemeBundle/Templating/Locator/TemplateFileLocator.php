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
use Sylius\Bundle\ThemeBundle\Locator\ResourceNotFoundException;
use Symfony\Component\Config\FileLocatorInterface;
use Symfony\Component\Templating\TemplateReferenceInterface;

/**
 * {@inheritdoc}
 *
 * @author Kamil Kokot <kamil.kokot@lakion.com>
 */
final class TemplateFileLocator implements FileLocatorInterface
{
    /**
     * @var FileLocatorInterface
     */
    private $decoratedFileLocator;

    /**
     * @var ThemeContextInterface
     */
    private $themeContext;

    /**
     * @var TemplateLocatorInterface
     */
    private $templateLocator;

    /**
     * @param FileLocatorInterface $decoratedFileLocator
     * @param ThemeContextInterface $themeContext
     * @param TemplateLocatorInterface $templateLocator
     */
    public function __construct(
        FileLocatorInterface $decoratedFileLocator,
        ThemeContextInterface $themeContext,
        TemplateLocatorInterface $templateLocator
    ) {
        $this->decoratedFileLocator = $decoratedFileLocator;
        $this->themeContext = $themeContext;
        $this->templateLocator = $templateLocator;
    }

    /**
     * {@inheritdoc}
     */
    public function locate($template, $currentPath = null, $first = true)
    {
        if (!$template instanceof TemplateReferenceInterface) {
            throw new \InvalidArgumentException('The template must be an instance of TemplateReferenceInterface.');
        }

        $themes = $this->themeContext->getThemeHierarchy();
        foreach ($themes as $theme) {
            try {
                return $this->templateLocator->locateTemplate($template, $theme);
            } catch (ResourceNotFoundException $exception) {
                // Ignore if resource cannot be found in given theme.
            }
        }

        return $this->decoratedFileLocator->locate($template, $currentPath, $first);
    }
}
