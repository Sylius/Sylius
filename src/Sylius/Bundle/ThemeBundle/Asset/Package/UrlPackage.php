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

namespace Sylius\Bundle\ThemeBundle\Asset\Package;

use Sylius\Bundle\ThemeBundle\Asset\PathResolverInterface;
use Sylius\Bundle\ThemeBundle\Context\ThemeContextInterface;
use Symfony\Component\Asset\Context\ContextInterface;
use Symfony\Component\Asset\Exception\InvalidArgumentException;
use Symfony\Component\Asset\UrlPackage as BaseUrlPackage;
use Symfony\Component\Asset\VersionStrategy\VersionStrategyInterface;

/**
 * @see BaseUrlPackage
 */
class UrlPackage extends BaseUrlPackage
{
    /**
     * @var array
     */
    private $baseUrls = [];

    /**
     * @var UrlPackage
     */
    private $sslPackage;

    /**
     * @var ThemeContextInterface
     */
    protected $themeContext;

    /**
     * @var PathResolverInterface
     */
    protected $pathResolver;

    /**
     * @param string|array $baseUrls Base asset URLs
     * @param VersionStrategyInterface $versionStrategy The version strategy
     * @param ThemeContextInterface $themeContext
     * @param PathResolverInterface $pathResolver
     * @param ContextInterface|null $context Context
     */
    public function __construct(
        $baseUrls,
        VersionStrategyInterface $versionStrategy,
        ThemeContextInterface $themeContext,
        PathResolverInterface $pathResolver,
        ?ContextInterface $context = null
    ) {
        parent::__construct($baseUrls, $versionStrategy, $context);

        if (!is_array($baseUrls)) {
            $baseUrls = (array) $baseUrls;
        }

        foreach ($baseUrls as $baseUrl) {
            $this->baseUrls[] = rtrim($baseUrl, '/');
        }

        $sslUrls = $this->getSslUrls($baseUrls);

        if ($sslUrls && $baseUrls !== $sslUrls) {
            $this->sslPackage = new self($sslUrls, $versionStrategy, $themeContext, $pathResolver);
        }

        $this->themeContext = $themeContext;
        $this->pathResolver = $pathResolver;
    }

    /**
     * {@inheritdoc}
     */
    public function getUrl($path): string
    {
        if ($this->isAbsoluteUrl($path)) {
            return $path;
        }

        if (null !== $this->sslPackage && $this->getContext()->isSecure()) {
            return $this->sslPackage->getUrl($path);
        }

        $theme = $this->themeContext->getTheme();
        if (null !== $theme) {
            $path = $this->pathResolver->resolve($path, $theme);
        }

        $url = $this->getVersionStrategy()->applyVersion($path);

        if ($this->isAbsoluteUrl($url)) {
            return $url;
        }

        if ($url && '/' != $url[0]) {
            $url = '/' . $url;
        }

        return $this->getBaseUrl($path) . $url;
    }

    /**
     * @param array $urls
     *
     * @return array
     */
    private function getSslUrls(array $urls): array
    {
        $sslUrls = [];

        foreach ($urls as $url) {
            if ('https://' === substr($url, 0, 8) || '//' === substr($url, 0, 2)) {
                $sslUrls[] = $url;
            } elseif ('http://' !== substr($url, 0, 7)) {
                throw new InvalidArgumentException(sprintf('"%s" is not a valid URL', $url));
            }
        }

        return $sslUrls;
    }
}
