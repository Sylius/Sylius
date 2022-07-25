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

namespace Sylius\Bundle\UiBundle\Registry;

use Laminas\Stdlib\SplPriorityQueue;

/**
 * @experimental
 */
final class TemplateBlockRegistry implements TemplateBlockRegistryInterface
{
    /**
     * Blocks within an event should be sorted by their priority descending.
     *
     * @var TemplateBlock[][]
     * @psalm-var array<string, array<string, TemplateBlock>>
     */
    private $eventsToTemplateBlocks;

    public function __construct(array $eventsToTemplateBlocks)
    {
        $this->eventsToTemplateBlocks = $eventsToTemplateBlocks;
    }

    public function all(): array
    {
        return $this->eventsToTemplateBlocks;
    }

    public function findEnabledForEvents(array $eventNames): array
    {
        // No need to sort blocks again if there's only one event because we have them sorted already
        if (count($eventNames) === 1) {
            $eventName = reset($eventNames);

            return array_values(array_filter(
                $this->eventsToTemplateBlocks[$eventName] ?? [],
                static fn (TemplateBlock $templateBlock): bool => $templateBlock->isEnabled(),
            ));
        }

        $enabledFinalizedTemplateBlocks = array_filter(
            $this->findFinalizedForEvents($eventNames),
            static fn (TemplateBlock $templateBlock): bool => $templateBlock->isEnabled(),
        );

        $templateBlocksPriorityQueue = new SplPriorityQueue();
        foreach ($enabledFinalizedTemplateBlocks as $templateBlock) {
            $templateBlocksPriorityQueue->insert($templateBlock, $templateBlock->getPriority());
        }

        return $templateBlocksPriorityQueue->toArray();
    }

    /**
     * @param string[] $eventNames
     * @psalm-param non-empty-list<string> $eventNames
     *
     * @return TemplateBlock[]
     */
    private function findFinalizedForEvents(array $eventNames): array
    {
        /**
         * @var TemplateBlock[]
         * @psalm-var array<string, TemplateBlock> $finalizedTemplateBlocks
         */
        $finalizedTemplateBlocks = [];
        $reversedEventNames = array_reverse($eventNames);
        foreach ($reversedEventNames as $eventName) {
            $templateBlocks = $this->eventsToTemplateBlocks[$eventName] ?? [];
            foreach ($templateBlocks as $blockName => $templateBlock) {
                if (array_key_exists($blockName, $finalizedTemplateBlocks)) {
                    $templateBlock = $finalizedTemplateBlocks[$blockName]->overwriteWith($templateBlock);
                }

                $finalizedTemplateBlocks[$blockName] = $templateBlock;
            }
        }

        return $finalizedTemplateBlocks;
    }
}
