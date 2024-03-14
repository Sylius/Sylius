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

namespace Sylius\Bundle\UiBundle\Registry;

use Laminas\Stdlib\SplPriorityQueue;

final class BlockRegistry implements BlockRegistryInterface
{
    /**
     * Blocks within an event should be sorted by their priority descending.
     *
     * @var array<string, array<string, Block>>
     */
    private array $eventsToBlocks;

    /**
     * @param array<string, array<string, Block>> $eventsToTemplateBlocks
     */
    public function __construct(array $eventsToTemplateBlocks)
    {
        $this->eventsToBlocks = $eventsToTemplateBlocks;
    }

    public function all(): array
    {
        return $this->eventsToBlocks;
    }

    public function findEnabledForEvents(array $eventNames): array
    {
        // No need to sort blocks again if there's only one event because we have them sorted already
        if (count($eventNames) === 1) {
            $eventName = reset($eventNames);

            return array_values(array_filter(
                $this->eventsToBlocks[$eventName] ?? [],
                static fn (Block $block): bool => $block->isEnabled(),
            ));
        }

        $enabledFinalizedBlocks = array_filter(
            $this->findFinalizedForEvents($eventNames),
            static fn (Block $block): bool => $block->isEnabled(),
        );

        $blocksPriorityQueue = new SplPriorityQueue();
        foreach ($enabledFinalizedBlocks as $block) {
            $blocksPriorityQueue->insert($block, $block->getPriority());
        }

        return $blocksPriorityQueue->toArray();
    }

    /**
     * @param string[] $eventNames
     *
     * @return Block[]
     */
    private function findFinalizedForEvents(array $eventNames): array
    {
        /** @var array<string, Block> $finalizedBlocks */
        $finalizedBlocks = [];
        $reversedEventNames = array_reverse($eventNames);
        foreach ($reversedEventNames as $eventName) {
            $blocks = $this->eventsToBlocks[$eventName] ?? [];
            foreach ($blocks as $blockName => $block) {
                if (array_key_exists($blockName, $finalizedBlocks)) {
                    $block = $finalizedBlocks[$blockName]->overwriteWith($block);
                }

                $finalizedBlocks[$blockName] = $block;
            }
        }

        return $finalizedBlocks;
    }
}

class_alias(BlockRegistry::class, '\Sylius\Bundle\UiBundle\Registry\TemplateBlockRegistry');
