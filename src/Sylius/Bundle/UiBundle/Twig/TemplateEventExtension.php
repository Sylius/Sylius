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

use Sylius\Bundle\UiBundle\Renderer\TemplateEventRendererInterface;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;
use Webmozart\Assert\Assert;

final class TemplateEventExtension extends AbstractExtension
{
    public function __construct(private TemplateEventRendererInterface $templateEventRenderer)
    {
    }

    public function getFunctions(): array
    {
        return [
            new TwigFunction('sylius_template_event', [$this, 'render'], ['is_safe' => ['html']]),
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

        return $this->templateEventRenderer->render($eventName, $context);
    }
}
