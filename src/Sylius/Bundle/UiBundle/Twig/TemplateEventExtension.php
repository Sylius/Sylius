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

namespace Sylius\Bundle\UiBundle\Twig;

use Sylius\Bundle\UiBundle\Renderer\TwigEventRendererInterface;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;
use Webmozart\Assert\Assert;

final class TemplateEventExtension extends AbstractExtension
{
    public function __construct(private TwigEventRendererInterface $templateEventRenderer)
    {
    }

    public function getFunctions(): array
    {
        return [
            new TwigFunction('sylius_template_event', [$this, 'render'], ['is_safe' => ['html']]),
            new TwigFunction('twig_event', [$this, 'render'], ['is_safe' => ['html']]),
            new TwigFunction('twig_event_name', [$this, 'createEventName']),
        ];
    }

    /**
     * @param string|string[] $eventName
     */
    public function render(array|string $eventName, array $context = []): string
    {
        if (is_string($eventName)) {
            $eventName = [$eventName];
        }
        Assert::notEmpty($eventName);

        /** @var non-empty-list<string> $eventName */
        $eventName = array_filter($eventName, fn (?string $eventName) => $eventName !== null);

        return $this->templateEventRenderer->render($eventName, $context);
    }

    public function createEventName(?string $eventName, string ...$suffixes): ?string
    {
        if ($eventName === null) {
            return null;
        }

        return implode('.', array_merge([$eventName], $suffixes));
    }
}
