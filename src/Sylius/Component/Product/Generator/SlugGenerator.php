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
    public function __construct(private ?SluggerInterface $slugger)
    {
        if (null === $this->slugger) {
            trigger_deprecation(
                'sylius/sylius',
                '2.3',
                'Not passing $slugger through constructor is deprecated and will be prohibited in Sylius 3.0.',
                self::class,
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
