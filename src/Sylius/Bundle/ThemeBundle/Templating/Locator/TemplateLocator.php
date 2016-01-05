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
use Sylius\Bundle\ThemeBundle\Locator\ResourceNotFoundException;
use Symfony\Component\Config\FileLocatorInterface;
use Symfony\Component\Templating\TemplateReferenceInterface;

/**
 * {@inheritdoc}
 *
 * @author Kamil Kokot <kamil.kokot@lakion.com>
 */
final class TemplateLocator implements FileLocatorInterface
{
    /**
     * @var FileLocatorInterface
     */
    private $templateLocator;

    /**
     * @var ThemeContextInterface
     */
    private $themeContext;

    /**
     * @var ResourceLocatorInterface
     */
    private $resourceLocator;

    /**
     * @param FileLocatorInterface $templateLocator
     * @param ThemeContextInterface $themeContext
     * @param ResourceLocatorInterface $resourceLocator
     */
    public function __construct(
        FileLocatorInterface $templateLocator,
        ThemeContextInterface $themeContext,
        ResourceLocatorInterface $resourceLocator
    ) {
        $this->templateLocator = $templateLocator;
        $this->themeContext = $themeContext;
        $this->resourceLocator = $resourceLocator;
    }

    /**
     * {@inheritdoc}
     */
    public function locate($template, $currentPath = null, $first = true)
    {
        if (!$template instanceof TemplateReferenceInterface) {
            throw new \InvalidArgumentException("The template must be an instance of TemplateReferenceInterface.");
        }

        $themes = $this->themeContext->getThemeHierarchy();
        foreach ($themes as $theme) {
            try {
                return $this->resourceLocator->locateResource($template->getPath(), $theme);
            } catch (ResourceNotFoundException $exception) {
                // Ignore if resource cannot be found in given theme.
            }
        }

        return $this->templateLocator->locate($template, $currentPath, $first);
    }
}
