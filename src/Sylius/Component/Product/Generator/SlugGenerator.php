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

namespace Sylius\Component\Product\Generator;

use Behat\Transliterator\Transliterator;
use Symfony\Component\String\Slugger\SluggerInterface;

final class SlugGenerator implements SlugGeneratorInterface
{
    public function __construct(private ?SluggerInterface $slugger = null)
    {
        if (null === $this->slugger) {
            trigger_deprecation(
                'sylius/product',
                '2.3',
                'Not passing a "%s" to "%s" is deprecated and will be required in Sylius 3.0.',
                SluggerInterface::class,
                self::class
            );
        }
    }

    public function generate(string $name): string
    {
        if (null !== $this->slugger) {
            return $this->slugger->slug($name)->lower()->toString();
        }

        // Manually replacing apostrophes since Transliterator started removing them at v1.2.
        $name = str_replace('\'', '-', $name);

        return Transliterator::transliterate($name);
    }
}
