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
    private $taxonRepository;

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
     * @param RepositoryInterface $taxonRepository
     * @param RepositoryInterface $taxonomyRepository
     * @param FactoryInterface $taxonomyFactory
     * @param TaxonFactoryInterface $taxonFactory
     * @param ObjectManager $objectManager
     */
    public function __construct(
        RepositoryInterface $taxonRepository,
        RepositoryInterface $taxonomyRepository,
        FactoryInterface $taxonomyFactory,
        TaxonFactoryInterface $taxonFactory,
        ObjectManager $objectManager
    ) {
        $this->taxonRepository = $taxonRepository;
        $this->taxonomyRepository = $taxonomyRepository;
        $this->taxonomyFactory = $taxonomyFactory;
        $this->taxonFactory = $taxonFactory;
        $this->objectManager = $objectManager;
    }

    /**
     * @Transform classified as :taxonName
     */
    public function getTaxonByName($taxonName)
    {
        $taxon = $this->taxonRepository->findOneBy(['name' => $taxonName]);
        if (null === $taxon) {
            throw new \InvalidArgumentException(sprintf('Taxon with name "%s" does not exist.', $taxonName));
        }

        return $taxon;
    }

    /**
     * @Given store classifies its products as :firstTaxonName
     * @Given store classifies its products as :firstTaxonName and :secondTaxonName
     * @Given store classifies its products as :firstTaxonName, :secondTaxonName and :thirdTaxonName
     */
    public function storeClassifiesItsProductsAs($firstTaxonName, $secondTaxonName = null, $thirdTaxonName = null)
    {
        /** @var TaxonomyInterface $taxonomy */
        $taxonomy = $this->taxonomyFactory->createNew();
        $taxonomy->setName('Category');
        $taxonomy->setCode('category');

        $taxonomy->addTaxon($this->createTaxon($firstTaxonName));

        if (null !== $secondTaxonName) {
            $taxonomy->addTaxon($this->createTaxon($secondTaxonName));
        }

        if (null !== $thirdTaxonName) {
            $taxonomy->addTaxon($this->createTaxon($thirdTaxonName));
        }

        $this->taxonomyRepository->add($taxonomy);
    }

    /**
     * @Given /^([^"]+) belongs to "([^"]+)"$/
     */
    public function itBelongsTo(ProductInterface $product, $taxonName)
    {
        $product->addTaxon($this->getTaxonByName($taxonName));

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
