<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\ThemeBundle\Locator;

/**
 * @author Kamil Kokot <kamil.kokot@lakion.com>
 */
class ApplicationResourceLocator implements ResourceLocatorInterface
{
    /**
     * @var PathCheckerInterface
     */
    private $pathChecker;

    /**
     * @var array
     */
    private $parameters;

    /**
     * @var array
     */
    private $paths = [
        '%theme_path%/%override_path%',
        '%app_path%/Resources/%override_path%',
    ];

    /**
     * @param string $appDir
     * @param PathCheckerInterface $pathChecker
     */
    public function __construct(PathCheckerInterface $pathChecker, $appDir)
    {
        $this->pathChecker = $pathChecker;
        $this->parameters = [
            "%app_path%" => $appDir,
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function locateResource($resourceName, array $themes = [])
    {
        $parameters = array_merge(
            $this->parameters,
            ['%override_path%' => $resourceName]
        );

        return $this->pathChecker->processPaths($this->paths, $parameters, $themes);
    }
}