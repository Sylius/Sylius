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

namespace Sylius\Bundle\UiBundle\DataCollector;

use Sylius\Bundle\UiBundle\Registry\Block;

/** @internal */
final class BlockRenderingHistory
{
    /** @var array<array{name: string, start: float, stop: float, time: float, blocks: list<array{definition: Block, start: float, stop: float, time: float}>}> */
    private $renderedEvents = [];

    /** @var array<array{name: string, start: float, stop?: float, time?: float, blocks: list<array{definition: Block, start: float, stop: float, time: float}>}> */
    private array $currentlyRenderedEvents = [];

    /** @var array<array{definition: Block, start: float, stop?: float, time?: float}> */
    private array $currentlyRenderedBlocks = [];

    /**
     * @param array<string> $eventNames
     * @param array<string, mixed> $context
     */
    public function startRenderingEvent(array $eventNames, array $context): void
    {
        $this->currentlyRenderedEvents[] = ['names' => $eventNames, 'start' => microtime(true), 'blocks' => []];
    }

    /**
     * @param array<string, mixed> $context
     */
    public function startRenderingBlock(Block $templateBlock, array $context): void
    {
        $this->currentlyRenderedBlocks[] = ['definition' => $templateBlock, 'start' => microtime(true)];
    }

    /**
     * @param array<string, mixed> $context
     */
    public function stopRenderingBlock(Block $templateBlock, array $context): void
    {
        $currentlyRenderedBlock = array_pop($this->currentlyRenderedBlocks);
        $currentlyRenderedBlock['stop'] = microtime(true);
        $currentlyRenderedBlock['time'] = $currentlyRenderedBlock['stop'] - $currentlyRenderedBlock['start'];

        $currentlyRenderedEvent = array_pop($this->currentlyRenderedEvents);
        $currentlyRenderedEvent['blocks'][] = $currentlyRenderedBlock;
        $this->currentlyRenderedEvents[] = $currentlyRenderedEvent;
    }

    /**
     * @param array<string> $eventNames
     * @param array<string, mixed> $context
     */
    public function stopRenderingEvent(array $eventNames, array $context): void
    {
        $currentlyRenderedEvent = array_pop($this->currentlyRenderedEvents);
        $currentlyRenderedEvent['stop'] = microtime(true);
        $currentlyRenderedEvent['time'] = $currentlyRenderedEvent['stop'] - $currentlyRenderedEvent['start'];
        $this->renderedEvents[] = $currentlyRenderedEvent;
    }

    /**
     * @return array<array{name: string, start: float, stop: float, time: float, blocks: list<array{definition: Block, start: float, stop: float, time: float}>}>
     */
    public function getRenderedEvents(): array
    {
        return $this->renderedEvents;
    }

    public function reset(): void
    {
        $this->renderedEvents = $this->currentlyRenderedEvents = $this->currentlyRenderedBlocks = [];
    }
}
