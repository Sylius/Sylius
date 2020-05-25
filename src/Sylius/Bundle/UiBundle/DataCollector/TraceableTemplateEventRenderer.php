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

use Sylius\Bundle\UiBundle\Renderer\TemplateEventRendererInterface;

/**
 * @internal
 * @experimental
 */
final class TraceableTemplateEventRenderer implements TemplateEventRendererInterface
{
    /** @var TemplateEventRendererInterface */
    private $templateEventRenderer;

    /** @var TemplateBlockRenderingHistory */
    private $templateBlockRenderingHistory;

    public function __construct(TemplateEventRendererInterface $templateEventRenderer, TemplateBlockRenderingHistory $templateBlockRenderingHistory)
    {
        $this->templateEventRenderer = $templateEventRenderer;
        $this->templateBlockRenderingHistory = $templateBlockRenderingHistory;
    }

    public function render(array $eventNames, array $context = []): string
    {
        $this->templateBlockRenderingHistory->startRenderingEvent($eventNames, $context);

        $renderedEvent = $this->templateEventRenderer->render($eventNames, $context);

        $this->templateBlockRenderingHistory->stopRenderingEvent($eventNames, $context);

        return $renderedEvent;
    }
}
