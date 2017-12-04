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

namespace Sylius\Component\Product\Generator;

use Behat\Transliterator\Transliterator;

final class SlugGenerator implements SlugGeneratorInterface
{
    /**
     * {@inheritdoc}
     */
    public function generate(string $name): string
    {
        // Manually replacing apostrophes since Transliterator started removing them at v1.2.
        $name = str_replace('\'', '-', $name);

        return Transliterator::transliterate($name);
    }
}
