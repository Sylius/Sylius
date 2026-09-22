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

use Behat\Transliterator\Transliterator;
use Sylius\Component\Taxonomy\Model\TaxonInterface;
use Symfony\Component\String\Slugger\SluggerInterface;
use Webmozart\Assert\Assert;

final class TaxonSlugGenerator implements TaxonSlugGeneratorInterface
{
    public function __construct(private ?SluggerInterface $slugger = null)
    {
        if (null === $this->slugger) {
            trigger_deprecation(
                'sylius/taxonomy',
                '2.3',
                'Not passing a "%s" to "%s" is deprecated and will be required in Sylius 3.0.',
                SluggerInterface::class,
                self::class,
            );
        }
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
        if (null !== $this->slugger) {
            return $this->slugger->slug($string)->lower()->toString();
        }

        // Manually replacing apostrophes since Transliterator started removing them at v1.2.
        return Transliterator::transliterate(str_replace('\'', '-', $string));
    }
}
