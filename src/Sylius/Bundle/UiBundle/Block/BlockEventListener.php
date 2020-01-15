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

final class BlockEventListener
{
    /** @var string */
    private $template;

    public function __construct(string $template)
    {
        @trigger_error(
            sprintf('Using "%s" to add blocks to the templates is deprecated since Sylius 1.7 and will be removed in Sylius 2.0. Use "sylius_ui" configuration instead.', self::class),
            \E_USER_DEPRECATED
        );

        $this->template = $template;
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
