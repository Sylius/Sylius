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

namespace Sylius\Bundle\UiBundle\Block;

use Sonata\BlockBundle\Event\BlockEvent;
use Sonata\BlockBundle\Model\Block;

/** @internal */
final class MultipleBlockEventListener
{
    /**
     * @var array
     *
     * @psalm-var array<string, array<array-key, array{template: string, name: string}>>
     */
    private $blocksForEvents;

    public function __construct(array $blocksForEvents)
    {
        $this->blocksForEvents = $blocksForEvents;
    }

    public function __invoke(BlockEvent $event, string $eventName): void
    {
        $eventName = str_replace('sonata.block.event.', '', $eventName);
        $blocks = $this->blocksForEvents[$eventName] ?? [];

        foreach ($blocks as $block) {
            $sonataBlock = new Block();
            $sonataBlock->setId(sprintf('%s=%s', $eventName, $block['name']));
            $sonataBlock->setSettings(array_replace($event->getSettings(), [
                'template' => $block['template'],
            ]));
            $sonataBlock->setType('sonata.block.service.template');

            $event->addBlock($sonataBlock);
        }
    }
}
