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

namespace Sylius\Bundle\AdminBundle\Generator;

use Behat\Transliterator\Transliterator;
use Sylius\Component\Core\Model\TaxonInterface;
use Sylius\Component\Taxonomy\Generator\TaxonSlugGeneratorInterface as BaseTaxonSlugGeneratorInterface;
use Symfony\Component\String\Slugger\SluggerInterface;

final readonly class TaxonSlugGenerator implements TaxonSlugGeneratorInterface
{
    public function __construct(
        private BaseTaxonSlugGeneratorInterface $slugGenerator,
        private ?SluggerInterface $slugger
    ) {
        if (null === $this->slugger) {
            trigger_deprecation(
                'sylius/sylius',
                '2.3',
                'Not passing $slugger through constructor is deprecated and will be prohibited in Sylius 3.0.',
                self::class,
            );
        }
    }

    public function generate(string $name, string $localeCode, ?TaxonInterface $parent = null): string
    {
        $slug = $this->transliterate($name);

        if (null === $parent) {
            return $slug;
        }

        $parentSlug = $this->slugGenerator->generate($parent, $localeCode);

        return $parentSlug . '/' . $slug;
    }

    private function transliterate(string $string): string
    {
        if (null !== $this->slugger) {
            return $this->slugger->slug($string)->lower()->toString();
        }

        // Manually replacing apostrophes since Transliterator started removing them at v1.2.
        return Transliterator::transliterate(str_replace('\'', '-', $string));
    }
}
