<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Component\Taxonomy\Factory;

use Sylius\Component\Resource\Factory\FactoryInterface;

/**
 * @author Magdalena Banasiak <magdalena.banasiak@lakion.com>
 */
class TaxonomyFactory implements FactoryInterface
{
    /**
     * @var FactoryInterface
     */
    private $taxonFactory;

    /**
     * @var FactoryInterface
     */
    private $translatableFactory;

    /**
     * @param FactoryInterface $taxonFactory
     */
    public function __construct(
        FactoryInterface $translatableFactory,
        FactoryInterface $taxonFactory
    ) {
        $this->translatableFactory = $translatableFactory;
        $this->taxonFactory = $taxonFactory;
    }

    /**
     * {@inheritdoc}
     */
    public function createNew()
    {
        $taxon = $this->taxonFactory->createNew();

        $taxonomy = $this->translatableFactory->createNew();
        $taxonomy->setRoot($taxon);

        return $taxonomy;
    }
}
