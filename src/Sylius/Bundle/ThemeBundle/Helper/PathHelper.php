<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\ThemeBundle\Helper;

use Sylius\Bundle\ThemeBundle\Context\ThemeContextInterface;
use Symfony\Component\Config\Definition\Exception\InvalidConfigurationException;

/**
 * @author Rafał Muszyński <rafal.muszynski@sourcefabric.org>
 */
final class PathHelper implements PathHelperInterface
{
    /**
     * @var ThemeContextInterface
     */
    private $themeContext;

    /**
     * @var bool
     */
    private $isContextAware;

    /**
     * @param ThemeContextInterface $themeContext
     * @param bool                  $isContextAware
     */
    public function __construct(ThemeContextInterface $themeContext, $isContextAware)
    {
        $this->themeContext = $themeContext;
        $this->isContextAware = $isContextAware;
    }

    /**
     * {@inheritdoc}
     */
    public function applySuffixFor(array $paths = [])
    {
        if (!$this->canBeContextAware()) {
            return $paths;
        }

        $contextAwarePaths = [];
        foreach ($paths as $path) {
            $paths[] = rtrim($path, DIRECTORY_SEPARATOR).DIRECTORY_SEPARATOR.$this->themeContext->getName();
        }

        return $contextAwarePaths;
    }

    /**
     * @return bool
     *
     * @throws InvalidConfigurationException
     */
    private function canBeContextAware()
    {
        $this->isContextAware = true;
        if ($this->isContextAware &&
            isset($this->themeContext) &&
            null === $this->themeContext->getName()
        ) {
            throw new InvalidConfigurationException(
                sprintf('You have enabled "context_aware_paths" setting but your "%s::getName()" method must return a name!', get_class($this->themeContext))
            );
        }

        if (!$this->isContextAware ||
            !isset($this->themeContext) ||
            null === $this->themeContext->getName()
        ) {
            return false;
        }

        return true;
    }
}
