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

namespace Sylius\Bundle\ThemeBundle\Loader;

use Sylius\Bundle\ThemeBundle\Model\ThemeInterface;

final class CircularDependencyFoundException extends \DomainException
{
    /**
     * @param ThemeInterface[] $themes
     * @param \Exception $previous
     */
    public function __construct(array $themes, ?\Exception $previous = null)
    {
        $cycle = $this->getCycleFromArray($themes);

        $message = sprintf(
            'Circular dependency was found while resolving theme "%s", caused by cycle "%s".',
            reset($themes)->getName(),
            $this->formatCycleToString($cycle)
        );

        parent::__construct($message, 0, $previous);
    }

    private function getCycleFromArray(array $themes): array
    {
        while (reset($themes) !== end($themes) || 1 === count($themes)) {
            array_shift($themes);
        }

        if (0 === count($themes)) {
            throw new \InvalidArgumentException('There is no cycle within given themes.');
        }

        return $themes;
    }

    private function formatCycleToString(array $themes): string
    {
        $themesNames = array_map(function (ThemeInterface $theme) {
            return $theme->getName();
        }, $themes);

        return implode(' -> ', $themesNames);
    }
}
