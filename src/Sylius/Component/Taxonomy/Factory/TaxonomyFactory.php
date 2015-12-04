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
use Sylius\Component\Taxonomy\Model\TaxonomyInterface;
use Sylius\Component\Translation\Factory\TranslatableFactory;
use Sylius\Component\Translation\Provider\LocaleProviderInterface;

/**
 * @author Magdalena Banasiak <magdalena.banasiak@lakion.com>
 */
class TaxonomyFactory extends TranslatableFactory implements FactoryInterface
{
    /**
     * @var FactoryInterface
     */
    private $taxonFactory;

    /**
     * @var string
     */
    private $className;

    /**
     * @param FactoryInterface $taxonFactory
     */
    public function __construct(
        FactoryInterface $translatableResourceFactory,
        LocaleProviderInterface $localeProvider,
        FactoryInterface $taxonFactory,
        $className
    ) {
        $this->taxonFactory = $taxonFactory;
        $this->className = $className;

        parent::__construct($translatableResourceFactory, $localeProvider);
    }

    /**
     * {@inheritdoc}
     */
    public function createNew()
    {
        $taxon = $this->taxonFactory->createNew();

        $taxonomy = parent::createNew();
        $taxonomy->setRoot($taxon);

        return $taxonomy;
    }
}
