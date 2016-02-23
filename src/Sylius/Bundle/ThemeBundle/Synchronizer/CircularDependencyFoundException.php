<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\ThemeBundle\Synchronizer;

use Sylius\Bundle\ThemeBundle\Model\ThemeInterface;

/**
 * @author Kamil Kokot <kamil.kokot@lakion.com>
 */
class CircularDependencyFoundException extends \DomainException
{
    /**
     * @param ThemeInterface[] $themes
     * @param int $code
     * @param \Exception $previous
     */
    public function __construct(array $themes, $code = 0, \Exception $previous = null)
    {
        $cycle = $this->getCycleAsString($themes);

        $message = sprintf(
            'Circular dependency was found while resolving theme "%s", caused by cycle "%s".',
            reset($themes)->getName(),
            $cycle
        );

        parent::__construct($message, $code, $previous);
    }

    /**
     * @param array $themes
     *
     * @return string
     */
    private function getCycleAsString(array $themes)
    {
        while (reset($themes) !== end($themes) || 1 === count($themes)) {
            array_shift($themes);
        }

        if (0 === count($themes)) {
            throw new \InvalidArgumentException('There is no cycle within given themes.');
        }

        return implode(' -> ', array_map(function (ThemeInterface $theme) { return $theme->getName(); }, $themes));
    }
}
