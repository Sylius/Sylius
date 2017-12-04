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

use Sylius\Bundle\ThemeBundle\Context\EmptyThemeContext;
use Sylius\Bundle\ThemeBundle\Context\ThemeContextInterface;
use Sylius\Bundle\ThemeBundle\HierarchyProvider\NoopThemeHierarchyProvider;
use Sylius\Bundle\ThemeBundle\HierarchyProvider\ThemeHierarchyProviderInterface;
use Sylius\Bundle\ThemeBundle\Locator\ResourceNotFoundException;
use Symfony\Component\Config\FileLocatorInterface;
use Symfony\Component\Templating\TemplateReferenceInterface;

/**
 * {@inheritdoc}
 */
final class TemplateFileLocator implements FileLocatorInterface, \Serializable
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
     * @var ThemeHierarchyProviderInterface
     */
    private $themeHierarchyProvider;

    /**
     * @var TemplateLocatorInterface
     */
    private $templateLocator;

    /**
     * @param FileLocatorInterface $decoratedFileLocator
     * @param ThemeContextInterface $themeContext
     * @param ThemeHierarchyProviderInterface $themeHierarchyProvider
     * @param TemplateLocatorInterface $templateLocator
     */
    public function __construct(
        FileLocatorInterface $decoratedFileLocator,
        ThemeContextInterface $themeContext,
        ThemeHierarchyProviderInterface $themeHierarchyProvider,
        TemplateLocatorInterface $templateLocator
    ) {
        $this->decoratedFileLocator = $decoratedFileLocator;
        $this->themeContext = $themeContext;
        $this->themeHierarchyProvider = $themeHierarchyProvider;
        $this->templateLocator = $templateLocator;
    }

    /**
     * {@inheritdoc}
     */
    public function locate($template, $currentPath = null, $first = true): string
    {
        if (!$template instanceof TemplateReferenceInterface) {
            throw new \InvalidArgumentException('The template must be an instance of TemplateReferenceInterface.');
        }

        $theme = $this->themeContext->getTheme();
        $themes = $theme !== null ? $this->themeHierarchyProvider->getThemeHierarchy($theme) : [];
        foreach ($themes as $theme) {
            try {
                return $this->templateLocator->locateTemplate($template, $theme);
            } catch (ResourceNotFoundException $exception) {
                // Ignore if resource cannot be found in given theme.
            }
        }

        return $this->decoratedFileLocator->locate($template, $currentPath, $first);
    }

    /**
     * {@inheritdoc}
     */
    public function serialize(): string
    {
        return serialize($this->decoratedFileLocator);
    }

    /**
     * {@inheritdoc}
     */
    public function unserialize($serialized): void
    {
        $this->decoratedFileLocator = unserialize($serialized);

        $this->themeContext = new EmptyThemeContext();
        $this->themeHierarchyProvider = new NoopThemeHierarchyProvider();
    }
}
