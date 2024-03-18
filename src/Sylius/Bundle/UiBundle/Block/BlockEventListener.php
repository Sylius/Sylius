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

namespace Sylius\Bundle\UiBundle\Block;

use Sonata\BlockBundle\Event\BlockEvent;
use Sonata\BlockBundle\Model\Block;

/**
 * @deprecated since Sylius 1.7 and will be removed in Sylius 2.0. Use "sylius_ui" configuration instead.
 */
final class BlockEventListener
{
    public function __construct(private string $template)
    {
        trigger_deprecation(
            'sylius/ui-bundle',
            '1.7',
            'Using "%s" to add blocks to the templates is deprecated and will be removed in Sylius 2.0. Use "sylius_ui" configuration instead.',
            self::class,
        );
    }

    public function onBlockEvent(BlockEvent $event): void
    {
        $block = new Block();
        $block->setId(uniqid('', true));
        $block->setSettings(array_replace($event->getSettings(), [
            'template' => $this->template,
        ]));
        $block->setType('sonata.block.service.template');

        $event->addBlock($block);
    }
}
