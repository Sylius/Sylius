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

namespace spec\Sylius\Component\Core\Positioner;

use PhpSpec\ObjectBehavior;
use Sylius\Component\Core\Model\PositionAwareInterface;

final class PositionerSpec extends ObjectBehavior
{
    public function it_returns_true_when_position_has_changed(PositionAwareInterface $positionAwareObject): void
    {
        $positionAwareObject->getPosition()->willReturn(0);

        $this->hasPositionChanged($positionAwareObject, 1)->shouldReturn(true);
    }

    public function it_returns_false_when_position_has_not_changed(PositionAwareInterface $positionAwareObject): void
    {
        $positionAwareObject->getPosition()->willReturn(0);

        $this->hasPositionChanged($positionAwareObject, 0)->shouldReturn(false);
    }

    public function it_updates_position_when_position_has_changed(PositionAwareInterface $positionAwareObject): void
    {
        $positionAwareObject->getPosition()->willReturn(0);
        $positionAwareObject->setPosition(1)->shouldBeCalled();

        $this->updatePosition($positionAwareObject, 1, 2);
    }

    public function it_does_not_update_position_when_position_has_not_changed(PositionAwareInterface $positionAwareObject): void
    {
        $positionAwareObject->getPosition()->willReturn(0);
        $positionAwareObject->setPosition(0)->shouldNotBeCalled();

        $this->updatePosition($positionAwareObject, 0, 2);
    }

    public function it_sets_new_position_to_minus_1_when_it_is_greater_than_max_position(PositionAwareInterface $positionAwareObject): void
    {
        $positionAwareObject->getPosition()->willReturn(0);
        $positionAwareObject->setPosition(-1)->shouldBeCalled();

        $this->updatePosition($positionAwareObject, 3, 2);
    }

    public function it_sets_new_position_to_minus_1_when_it_is_equal_to_max_position(PositionAwareInterface $positionAwareObject): void
    {
        $positionAwareObject->getPosition()->willReturn(0);
        $positionAwareObject->setPosition(-1)->shouldBeCalled();

        $this->updatePosition($positionAwareObject, 2, 2);
    }
}
