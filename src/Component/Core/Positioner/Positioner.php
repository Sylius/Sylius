<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Sylius Sp. z o.o.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Sylius\Component\Core\Positioner;

use Sylius\Component\Core\Model\PositionAwareInterface;

final class Positioner implements PositionerInterface
{
    public function updatePosition(PositionAwareInterface $positionAwareObject, int $newPosition, int $maxPosition): void
    {
        if (!$this->hasPositionChanged($positionAwareObject, $newPosition)) {
            return;
        }

        if ($newPosition >= $maxPosition) {
            $newPosition = -1;
        }

        $positionAwareObject->setPosition($newPosition);
    }

    public function hasPositionChanged(PositionAwareInterface $positionAwareObject, int $newPosition): bool
    {
        return $positionAwareObject->getPosition() !== $newPosition;
    }
}
