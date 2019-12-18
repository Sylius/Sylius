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

namespace Sylius\Bundle\UiBundle\Twig;

use Twig\Environment;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

final class TemplateEventExtension extends AbstractExtension
{
    /** @var Environment */
    private $twig;

    /**
     * @var array
     *
     * @psalm-var array<string, list<string>>
     */
    private $eventsToTemplates;

    public function __construct(Environment $twig, array $eventsToTemplates)
    {
        $this->twig = $twig;
        $this->eventsToTemplates = $eventsToTemplates;
    }

    public function getFunctions(): array
    {
        return [
            new TwigFunction('sylius_template_event', [$this, 'renderBlocksForEvent'], ['is_safe' => ['html']]),
        ];
    }

    public function renderBlocksForEvent(string $event, array $options = []): string
    {
        $templates = $this->eventsToTemplates[$event] ?? [];

        $renderedTemplates = [];

        foreach ($templates as $template) {
            $renderedTemplates[] = $this->twig->render($template, $options);
        }

        return implode("\n", $renderedTemplates);
    }
}
