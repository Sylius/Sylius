<?php

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

        $assetPath = $path;
        foreach ($this->themeContext->getThemesSortedByPriorityInDescendingOrder() as $theme) {
            $availableAssetPath = $this->pathResolver->resolve($path, $theme);

            if (file_exists($availableAssetPath)) {
                $assetPath = $availableAssetPath;
                break;
            }
        }

        return $this->getBasePath() . ltrim($this->getVersionStrategy()->applyVersion($assetPath), '/');
    }
}