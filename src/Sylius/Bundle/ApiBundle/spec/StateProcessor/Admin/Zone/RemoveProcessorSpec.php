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

namespace spec\Sylius\Bundle\ApiBundle\StateProcessor\Admin\Zone;

use ApiPlatform\Metadata\Delete;
use ApiPlatform\State\ProcessorInterface;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Sylius\Bundle\ApiBundle\Exception\ZoneCannotBeRemoved;
use Sylius\Component\Addressing\Checker\ZoneDeletionCheckerInterface;
use Sylius\Component\Addressing\Model\ZoneInterface;

final class RemoveProcessorSpec extends ObjectBehavior
{
    function let(
        ProcessorInterface $removeProcessor,
        ZoneDeletionCheckerInterface $zoneDeletionChecker,
    ): void {
        $this->beConstructedWith($removeProcessor, $zoneDeletionChecker);
    }

    function it_throws_an_exception_if_object_is_not_a_zone(
        ProcessorInterface $removeProcessor,
        ZoneDeletionCheckerInterface $zoneDeletionChecker,
    ): void {
        $zoneDeletionChecker->isDeletable(Argument::any())->shouldNotBeCalled();
        $removeProcessor->process(Argument::cetera())->shouldNotBeCalled();

        $this
            ->shouldThrow(\InvalidArgumentException::class)
            ->during('process', [new \stdClass(), new Delete(), [], []])
        ;
    }

    function it_throws_exception_if_zone_is_not_deletable(
        ProcessorInterface $removeProcessor,
        ZoneDeletionCheckerInterface $zoneDeletionChecker,
        ZoneInterface $zone,
    ): void {
        $zoneDeletionChecker->isDeletable($zone)->willReturn(false);

        $removeProcessor->process(Argument::cetera())->shouldNotBeCalled();

        $this
            ->shouldThrow(ZoneCannotBeRemoved::class)
            ->during('process', [$zone, new Delete(), [], []])
        ;
    }

    function it_uses_decorated_data_persister_to_remove_channel(
        ProcessorInterface $removeProcessor,
        ZoneDeletionCheckerInterface $zoneDeletionChecker,
        ZoneInterface $zone,
    ): void {
        $operation = new Delete();
        $zoneDeletionChecker->isDeletable($zone)->willReturn(true);

        $removeProcessor->process($zone, $operation, [], [])->willReturn($zone);

        $this->process($zone, $operation);
    }
}
