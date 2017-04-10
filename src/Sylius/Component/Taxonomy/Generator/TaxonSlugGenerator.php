<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Component\Taxonomy\Generator;

use Behat\Transliterator\Transliterator;
use Sylius\Component\Taxonomy\Model\TaxonInterface;
use Sylius\Component\Taxonomy\Repository\TaxonRepositoryInterface;
use Webmozart\Assert\Assert;

/**
 * @author Mateusz Zalewski <mateusz.zalewski@lakion.com>
 */
final class TaxonSlugGenerator implements TaxonSlugGeneratorInterface
{
    const SLUG_SEPARATOR = '/';

    /**
     * @var TaxonRepositoryInterface
     */
    private $taxonRepository;

    /**
     * @param TaxonRepositoryInterface $taxonRepository
     */
    public function __construct(TaxonRepositoryInterface $taxonRepository)
    {
        $this->taxonRepository = $taxonRepository;
    }

    /**
     * {@inheritdoc}
     */
    public function generate($name, $parentId = null)
    {
        // Manually replacing apostrophes since Transliterator started removing them at v1.2.
        $name = str_replace('\'', '-', $name);
        $taxonSlug = Transliterator::transliterate($name);
        if (null === $parentId) {
            return $taxonSlug;
        }

        /** @var TaxonInterface $parent */
        $parent = $this->taxonRepository->find($parentId);
        Assert::notNull($parent, sprintf('There is no parent taxon with id %d.', $parentId));

        return $parent->getSlug().self::SLUG_SEPARATOR.$taxonSlug;
    }
}
