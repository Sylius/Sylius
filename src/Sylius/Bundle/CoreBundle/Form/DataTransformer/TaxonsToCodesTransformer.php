<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\CoreBundle\Form\DataTransformer;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Sylius\Component\Core\Model\TaxonInterface;
use Sylius\Component\Resource\Exception\UnexpectedTypeException;
use Sylius\Component\Taxonomy\Repository\TaxonRepositoryInterface;
use Symfony\Component\Form\DataTransformerInterface;

/**
 * @author Mateusz Zalewski <mateusz.zalewski@lakion.com>
 */
class TaxonsToCodesTransformer implements DataTransformerInterface
{
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
    public function transform($value)
    {
        if (!is_array($value)) {
            throw new UnexpectedTypeException($value, 'array');
        }

        if (empty($value)) {
            return new ArrayCollection();
        }

        return new ArrayCollection($this->taxonRepository->findBy(['code' => $value['taxons']]));
    }

    /**
     * {@inheritdoc}
     */
    public function reverseTransform($value)
    {
        if (!$value instanceof Collection) {
            throw new UnexpectedTypeException($value, Collection::class);
        }

        $taxons = $value->get('taxons');

        if (null === $taxons) {
            return [];
        }

        if (!(is_array($taxons) || $taxons instanceof \Traversable)) {
            throw new \InvalidArgumentException('"taxons" element of collection should be Traversable');
        }

        $taxonCodes = [];

        /** @var TaxonInterface $taxon */
        foreach ($taxons as $taxon) {
            $taxonCodes[] = $taxon->getCode();
        }

        return ['taxons' => $taxonCodes];
    }
}
