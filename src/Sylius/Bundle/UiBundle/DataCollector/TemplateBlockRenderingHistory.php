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

namespace Sylius\Bundle\UiBundle\DataCollector;

use Sylius\Bundle\UiBundle\Registry\TemplateBlock;

/**
 * @internal
 * @experimental
 */
final class TemplateBlockRenderingHistory
{
    /** @psalm-var list<array{name: string, start: float, stop: float, time: float, blocks: list<array{definition: TemplateBlock, start: float, stop: float, time: float}>}> */
    private $renderedEvents = [];

    /** @psalm-var list<array{name: string, start: float, stop?: float, time?: float, blocks: list<array{definition: TemplateBlock, start: float, stop: float, time: float}>}> */
    private $currentlyRenderedEvents = [];

    /** @psalm-var list<array{definition: TemplateBlock, start: float, stop?: float, time?: float}> */
    private $currentlyRenderedBlocks = [];

    public function startRenderingEvent(array $eventNames, array $context): void
    {
        $this->currentlyRenderedEvents[] = ['names' => $eventNames, 'start' => microtime(true), 'blocks' => []];
    }

    public function startRenderingBlock(TemplateBlock $templateBlock, array $context): void
    {
        $this->currentlyRenderedBlocks[] = ['definition' => $templateBlock, 'start' => microtime(true)];
    }

    public function stopRenderingBlock(TemplateBlock $templateBlock, array $context): void
    {
        $currentlyRenderedBlock = array_pop($this->currentlyRenderedBlocks);
        $currentlyRenderedBlock['stop'] = microtime(true);
        $currentlyRenderedBlock['time'] = $currentlyRenderedBlock['stop'] - $currentlyRenderedBlock['start'];

        $currentlyRenderedEvent = array_pop($this->currentlyRenderedEvents);
        $currentlyRenderedEvent['blocks'][] = $currentlyRenderedBlock;
        $this->currentlyRenderedEvents[] = $currentlyRenderedEvent;
    }

    public function stopRenderingEvent(array $eventNames, array $context): void
    {
        $currentlyRenderedEvent = array_pop($this->currentlyRenderedEvents);
        $currentlyRenderedEvent['stop'] = microtime(true);
        $currentlyRenderedEvent['time'] = $currentlyRenderedEvent['stop'] - $currentlyRenderedEvent['start'];
        $this->renderedEvents[] = $currentlyRenderedEvent;
    }

    public function getRenderedEvents(): array
    {
        return $this->renderedEvents;
    }

    public function reset(): void
    {
        $this->renderedEvents = $this->currentlyRenderedEvents = $this->currentlyRenderedBlocks = [];
    }
}
