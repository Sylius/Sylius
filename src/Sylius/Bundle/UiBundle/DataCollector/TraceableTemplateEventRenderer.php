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

use Sylius\Bundle\UiBundle\Renderer\TemplateEventRendererInterface;

/** @internal */
final class TraceableTemplateEventRenderer implements TemplateEventRendererInterface
{
    public function __construct(
        private TemplateEventRendererInterface $templateEventRenderer,
        private TemplateBlockRenderingHistory $templateBlockRenderingHistory,
    ) {
    }

    public function render(array $eventNames, array $context = []): string
    {
        $this->templateBlockRenderingHistory->startRenderingEvent($eventNames, $context);

        $renderedEvent = $this->templateEventRenderer->render($eventNames, $context);

        $this->templateBlockRenderingHistory->stopRenderingEvent($eventNames, $context);

        return $renderedEvent;
    }
}
