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

use Sonata\BlockBundle\Templating\Helper\BlockHelper;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

/**
 * Proxy extension for rendering blocks for an event - currently uses Sonata, might change in the future.
 */
final class TemplateEventExtension extends AbstractExtension
{
    /** @var BlockHelper */
    private $blockHelper;

    public function __construct(BlockHelper $blockHelper)
    {
        $this->blockHelper = $blockHelper;
    }

    public function getFunctions(): array
    {
        return [
            new TwigFunction('sylius_template_event', [$this, 'renderBlocksForEvent'], ['is_safe' => ['html']]),
        ];
    }

    public function renderBlocksForEvent(string $event, array $options = []): string
    {
        return $this->blockHelper->renderEvent($event, $options);
    }
}
