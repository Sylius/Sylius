<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\ThemeBundle\Asset\Package;

use Sylius\Bundle\ThemeBundle\Asset\PathResolverInterface;
use Sylius\Bundle\ThemeBundle\Context\ThemeContextInterface;
use Symfony\Component\Asset\Context\ContextInterface;
use Symfony\Component\Asset\PathPackage as BasePathPackage;
use Symfony\Component\Asset\VersionStrategy\VersionStrategyInterface;

/**
 * @author Kamil Kokot <kamil.kokot@lakion.com>
 */
class PathPackage extends BasePathPackage
{
    /**
     * @var ThemeContextInterface
     */
    protected $themeContext;

    /**
     * @var PathResolverInterface
     */
    protected $pathResolver;

    /**
     * @param string $basePath
     * @param VersionStrategyInterface $versionStrategy
     * @param ThemeContextInterface $themeContext
     * @param PathResolverInterface $pathResolver
     * @param ContextInterface|null $context
     */
    public function __construct(
        $basePath,
        VersionStrategyInterface $versionStrategy,
        ThemeContextInterface $themeContext,
        PathResolverInterface $pathResolver,
        ContextInterface $context = null
    ) {
        parent::__construct($basePath, $versionStrategy, $context);

        $this->themeContext = $themeContext;
        $this->pathResolver = $pathResolver;
    }

    /**
     * {@inheritdoc}
     */
    public function getUrl($path)
    {
        if ($this->isAbsoluteUrl($path)) {
            return $path;
        }

        $theme = $this->themeContext->getTheme();
        if (null !== $theme) {
            $path = $this->pathResolver->resolve($path, $theme);
        }

        return $this->getBasePath().ltrim($this->getVersionStrategy()->applyVersion($path), '/');
    }
}
