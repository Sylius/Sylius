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
use Behat\Mink\Element\NodeElement;
use Doctrine\Common\Persistence\ObjectManager;
use Sylius\Component\Core\Model\ProductInterface;
use Sylius\Component\Core\Model\TaxonInterface;
use Sylius\Component\Core\Uploader\ImageUploaderInterface;
use Sylius\Component\Resource\Factory\FactoryInterface;
use Sylius\Component\Resource\Repository\RepositoryInterface;
use Symfony\Component\HttpFoundation\File\UploadedFile;

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
     * @var FactoryInterface
     */
    private $taxonImageFactory;

    /**
     * @var ObjectManager
     */
    private $objectManager;

    /**
     * @var ImageUploaderInterface
     */
    private $imageUploader;

    /**
     * @var array
     */
    private $minkParameters;

    /**
     * @param RepositoryInterface $taxonRepository
     * @param FactoryInterface $taxonFactory
     * @param FactoryInterface $taxonImageFactory
     * @param ObjectManager $objectManager
     * @param ImageUploaderInterface $imageUploader
     * @param array $minkParameters
     */
    public function __construct(
        RepositoryInterface $taxonRepository,
        FactoryInterface $taxonFactory,
        FactoryInterface $taxonImageFactory,
        ObjectManager $objectManager,
        ImageUploaderInterface $imageUploader,
        array $minkParameters
    ) {
        $this->taxonRepository = $taxonRepository;
        $this->taxonFactory = $taxonFactory;
        $this->taxonImageFactory = $taxonImageFactory;
        $this->objectManager = $objectManager;
        $this->imageUploader = $imageUploader;
        $this->minkParameters = $minkParameters;
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
     * @Given /^the ("[^"]+" taxon) has(?:| also) an image "([^"]+)" with a code "([^"]+)"$/
     */
    public function theTaxonHasAnImageWithACode(TaxonInterface $taxon, $imagePath, $imageCode)
    {
        $filesPath = $this->getParameter('files_path');

        $taxonImage = $this->taxonImageFactory->createNew();
        $taxonImage->setFile(new UploadedFile($filesPath.$imagePath, basename($imagePath)));
        $taxonImage->setCode($imageCode);
        $this->imageUploader->upload($taxonImage);

        $taxon->addImage($taxonImage);

        $this->objectManager->flush($taxon);
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

    /**
     * @param string $name
     *
     * @return NodeElement
     */
    private function getParameter($name)
    {
        return isset($this->minkParameters[$name]) ? $this->minkParameters[$name] : null;
    }
}
