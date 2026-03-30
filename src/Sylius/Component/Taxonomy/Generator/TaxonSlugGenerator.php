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

namespace Sylius\Component\Taxonomy\Generator;

use Sylius\Component\Taxonomy\Model\TaxonInterface;
use Symfony\Component\String\Slugger\SluggerInterface;
use Webmozart\Assert\Assert;

final class TaxonSlugGenerator implements TaxonSlugGeneratorInterface
{
    public function __construct(private SluggerInterface $slugger)
    {
    }

    public function generate(TaxonInterface $taxon, ?string $locale = null): string
    {
        $name = $taxon->getTranslation($locale)->getName();

        Assert::notEmpty($name, 'Cannot generate slug without a name.');

        $slug = $this->transliterate($name);

        $parentTaxon = $taxon->getParent();
        if (null === $parentTaxon) {
            return $slug;
        }

        $parentSlug = $parentTaxon->getTranslation($locale)->getSlug() ?: $this->generate($parentTaxon, $locale);

        return $parentSlug . '/' . $slug;
    }

    private function transliterate(string $string): string
    {
        return $this->slugger->slug(str_replace('\'', '-', $string))->lower()->toString();
    }
}
