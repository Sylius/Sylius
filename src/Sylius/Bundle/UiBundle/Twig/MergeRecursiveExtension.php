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

use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;

final class MergeRecursiveExtension extends AbstractExtension
{
    public function getFilters(): array
    {
        return [
            new TwigFilter(
                'sylius_merge_recursive',
                function (array $firstArray, array $secondArray): array {
                    return array_merge_recursive($firstArray, $secondArray);
                }
            ),
        ];
    }
}
