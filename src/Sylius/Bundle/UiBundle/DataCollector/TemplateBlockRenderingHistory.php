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
 */
final class TemplateBlockRenderingHistory
{
    /** @psalm-var list<array{name: string, start: float, stop: float, time: float, blocks: list<array{definition: TemplateBlock, start: float, stop: float, time: float}>}> */
    private $renderedEvents = [];

    /** @psalm-var array{name: string, start: float, stop?: float, time?: float, blocks: list<array{definition: TemplateBlock, start: float, stop: float, time: float}>} */
    private $currentlyRenderedEvent = [];

    /** @psalm-var array{definition: TemplateBlock, start: float, stop?: float, time?: float} */
    private $currentlyRenderedBlock = [];

    public function startRenderingEvent(string $eventName, array $context): void
    {
        $this->currentlyRenderedEvent = ['name' => $eventName, 'start' => microtime(true), 'blocks' => []];
    }

    public function startRenderingBlock(string $eventName, TemplateBlock $templateBlock, array $context): void
    {
        $this->currentlyRenderedBlock = ['definition' => $templateBlock, 'start' => microtime(true)];
    }

    public function stopRenderingBlock(string $eventName, TemplateBlock $templateBlock, array $context): void
    {
        $this->currentlyRenderedBlock['stop'] = microtime(true);
        $this->currentlyRenderedBlock['time'] = $this->currentlyRenderedBlock['stop'] - $this->currentlyRenderedBlock['start'];
        $this->currentlyRenderedEvent['blocks'][] = $this->currentlyRenderedBlock;
        $this->currentlyRenderedBlock = [];
    }

    public function stopRenderingEvent(string $eventName, array $context): void
    {
        $this->currentlyRenderedEvent['stop'] = microtime(true);
        $this->currentlyRenderedEvent['time'] = $this->currentlyRenderedEvent['stop'] - $this->currentlyRenderedEvent['start'];
        $this->renderedEvents[] = $this->currentlyRenderedEvent;
        $this->currentlyRenderedEvent = [];
    }

    public function getRenderedEvents(): array
    {
        return $this->renderedEvents;
    }

    public function reset(): void
    {
        $this->renderedEvents = $this->currentlyRenderedEvent = $this->currentlyRenderedBlock = [];
    }
}
