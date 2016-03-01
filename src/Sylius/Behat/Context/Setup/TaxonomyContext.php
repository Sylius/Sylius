<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Behat\Context\Setup;

use Behat\Behat\Context\Context;
use Doctrine\Common\Persistence\ObjectManager;
use Sylius\Component\Core\Model\ProductInterface;
use Sylius\Component\Core\Model\TaxonInterface;
use Sylius\Component\Resource\Factory\FactoryInterface;
use Sylius\Component\Resource\Repository\RepositoryInterface;
use Sylius\Component\Taxonomy\Factory\TaxonFactoryInterface;
use Sylius\Component\Taxonomy\Model\TaxonomyInterface;

/**
 * @author Mateusz Zalewski <mateusz.zalewski@lakion.com>
 */
class TaxonomyContext implements Context
{
    /**
     * @var RepositoryInterface
     */
    private $taxonomyRepository;

    /**
     * @var FactoryInterface
     */
    private $taxonomyFactory;

    /**
     * @var TaxonFactoryInterface
     */
    private $taxonFactory;

    /**
     * @var ObjectManager
     */
    private $objectManager;

    /**
     * @param RepositoryInterface $taxonomyRepository
     * @param FactoryInterface $taxonomyFactory
     * @param TaxonFactoryInterface $taxonFactory
     * @param ObjectManager $objectManager
     */
    public function __construct(
        RepositoryInterface $taxonomyRepository,
        FactoryInterface $taxonomyFactory,
        TaxonFactoryInterface $taxonFactory,
        ObjectManager $objectManager
    ) {
        $this->taxonomyRepository = $taxonomyRepository;
        $this->taxonomyFactory = $taxonomyFactory;
        $this->taxonFactory = $taxonFactory;
        $this->objectManager = $objectManager;
    }

    /**
     * @Given the store classifies its products as :firstTaxonName
     * @Given the store classifies its products as :firstTaxonName and :secondTaxonName
     * @Given the store classifies its products as :firstTaxonName, :secondTaxonName and :thirdTaxonName
     */
    public function storeClassifiesItsProductsAs($firstTaxonName, $secondTaxonName = null, $thirdTaxonName = null)
    {
        /** @var TaxonomyInterface $taxonomy */
        $taxonomy = $this->taxonomyFactory->createNew();
        $taxonomy->setName('Category');
        $taxonomy->setCode('category');

        foreach ([$firstTaxonName, $secondTaxonName, $thirdTaxonName] as $taxonName) {
            if (null === $taxonName) {
                break;
            }

            $taxonomy->addTaxon($this->createTaxon($taxonName));
        }

        $this->taxonomyRepository->add($taxonomy);
    }

    /**
     * @Given /^(it) (belongs to "[^"]+")$/
     */
    public function itBelongsTo(ProductInterface $product, TaxonInterface $taxon)
    {
        $product->addTaxon($taxon);

        $this->objectManager->flush($product);
    }

    /**
     * @param string $name
     *
     * @return TaxonInterface
     */
    private function createTaxon($name)
    {
        $taxon = $this->taxonFactory->createNew();
        $taxon->setName($name);
        $taxon->setCode($this->getCodeFromName($name));

        return $taxon;
    }

    /**
     * @param string $name
     *
     * @return string
     */
    private function getCodeFromName($name)
    {
        return str_replace([' ', '-'], '_', strtolower($name));
    }
}
