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

namespace Sylius\Bundle\UiBundle\Registry;

final class TemplateBlockRegistry implements TemplateBlockRegistryInterface
{
    /** @psalm-var array<string, list<TemplateBlock>> */
    private $eventsToTemplateBlocks;

    public function __construct(array $eventsToTemplateBlocks)
    {
        $this->eventsToTemplateBlocks = $eventsToTemplateBlocks;
    }

    public function all(): array
    {
        return $this->eventsToTemplateBlocks;
    }

    public function findEnabledForEvent(string $eventName): array
    {
        return array_values(array_filter(
            $this->eventsToTemplateBlocks[$eventName] ?? [],
            static function (TemplateBlock $templateBlock): bool {
                return $templateBlock->enabled();
            }
        ));
    }
}
