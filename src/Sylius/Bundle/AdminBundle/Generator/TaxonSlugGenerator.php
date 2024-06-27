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

final readonly class TaxonSlugGenerator implements TaxonSlugGeneratorInterface
{

    public function __construct(private BaseTaxonSlugGeneratorInterface $slugGenerator)
    {
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
        // Manually replacing apostrophes since Transliterator started removing them at v1.2.
        return Transliterator::transliterate(str_replace('\'', '-', $string));
    }
}
