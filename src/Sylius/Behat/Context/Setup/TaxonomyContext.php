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

/**
 * @author Mateusz Zalewski <mateusz.zalewski@lakion.com>
 */
final class TaxonomyContext implements Context
{
    /**
     * @var RepositoryInterface
     */
    private $taxonRepository;

    /**
     * @var FactoryInterface
     */
    private $taxonFactory;

    /**
     * @var ObjectManager
     */
    private $objectManager;

    /**
     * @param RepositoryInterface $taxonRepository
     * @param FactoryInterface $taxonFactory
     * @param ObjectManager $objectManager
     */
    public function __construct(
        RepositoryInterface $taxonRepository,
        FactoryInterface $taxonFactory,
        ObjectManager $objectManager
    ) {
        $this->taxonRepository = $taxonRepository;
        $this->taxonFactory = $taxonFactory;
        $this->objectManager = $objectManager;
    }

    /**
     * @Given the store has :firstTaxonName taxonomy
     * @Given the store classifies its products as :firstTaxonName
     * @Given the store classifies its products as :firstTaxonName and :secondTaxonName
     * @Given the store classifies its products as :firstTaxonName, :secondTaxonName and :thirdTaxonName
     * @Given the store classifies its products as :firstTaxonName, :secondTaxonName, :thirdTaxonName and :fourthTaxonName
     */
    public function storeClassifiesItsProductsAs(
        $firstTaxonName,
        $secondTaxonName = null,
        $thirdTaxonName = null,
        $fourthTaxonName = null
    ) {
        foreach ([$firstTaxonName, $secondTaxonName, $thirdTaxonName, $fourthTaxonName] as $taxonName) {
            if (null === $taxonName) {
                break;
            }

            $this->taxonRepository->add($this->createTaxon($taxonName));
        }
    }

    /**
     * @Given /^(it|this product) (belongs to "[^"]+")$/
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
