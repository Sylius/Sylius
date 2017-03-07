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
use Sylius\Component\Core\Formatter\StringInflector;
use Sylius\Component\Core\Model\ImageInterface;
use Sylius\Component\Core\Model\TaxonInterface;
use Sylius\Component\Core\Uploader\ImageUploaderInterface;
use Sylius\Component\Resource\Factory\FactoryInterface;
use Sylius\Component\Resource\Model\TranslationInterface;
use Sylius\Component\Resource\Repository\RepositoryInterface;
use Sylius\Component\Taxonomy\Generator\TaxonSlugGeneratorInterface;
use Sylius\Component\Taxonomy\Model\TaxonTranslationInterface;
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
    private $taxonTranslationFactory;

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
     * @var TaxonSlugGeneratorInterface
     */
    private $taxonSlugGenerator;

    /**
     * @var array
     */
    private $minkParameters;

    /**
     * @param RepositoryInterface $taxonRepository
     * @param FactoryInterface $taxonFactory
     * @param FactoryInterface $taxonTranslationFactory
     * @param FactoryInterface $taxonImageFactory
     * @param ObjectManager $objectManager
     * @param ImageUploaderInterface $imageUploader
     * @param TaxonSlugGeneratorInterface $taxonSlugGenerator
     * @param array $minkParameters
     */
    public function __construct(
        RepositoryInterface $taxonRepository,
        FactoryInterface $taxonFactory,
        FactoryInterface $taxonTranslationFactory,
        FactoryInterface $taxonImageFactory,
        ObjectManager $objectManager,
        ImageUploaderInterface $imageUploader,
        TaxonSlugGeneratorInterface $taxonSlugGenerator,
        array $minkParameters
    ) {
        $this->taxonRepository = $taxonRepository;
        $this->taxonFactory = $taxonFactory;
        $this->taxonTranslationFactory = $taxonTranslationFactory;
        $this->taxonImageFactory = $taxonImageFactory;
        $this->objectManager = $objectManager;
        $this->imageUploader = $imageUploader;
        $this->taxonSlugGenerator = $taxonSlugGenerator;
        $this->minkParameters = $minkParameters;
    }

    /**
     * @Given the store has :firstTaxonName taxonomy
     * @Given the store classifies its products as :firstTaxonName
     * @Given the store classifies its products as :firstTaxonName and :secondTaxonName
     * @Given the store classifies its products as :firstTaxonName, :secondTaxonName and :thirdTaxonName
     * @Given the store classifies its products as :firstTaxonName, :secondTaxonName, :thirdTaxonName and :fourthTaxonName
     */
    public function storeClassifiesItsProductsAs(...$taxonsNames)
    {
        foreach ($taxonsNames as $taxonName) {
            $this->taxonRepository->add($this->createTaxon($taxonName));
        }
    }

    /**
     * @Given /^the store has taxonomy named "([^"]+)" in ("[^"]+" locale) and "([^"]+)" in ("[^"]+" locale)$/
     */
    public function theStoreHasTaxonomyNamedInAndIn($firstName, $firstLocale, $secondName, $secondLocale)
    {
        $translationMap = [
            $firstLocale => $firstName,
            $secondLocale => $secondName,
        ];

        $this->taxonRepository->add($this->createTaxonInManyLanguages($translationMap));
    }

    /**
     * @Given /^the ("[^"]+" taxon)(?:| also) has an image "([^"]+)" with "([^"]+)" type$/
     */
    public function theTaxonHasAnImageWithType(TaxonInterface $taxon, $imagePath, $imageType)
    {
        $filesPath = $this->getParameter('files_path');

        /** @var ImageInterface $taxonImage */
        $taxonImage = $this->taxonImageFactory->createNew();
        $taxonImage->setFile(new UploadedFile($filesPath.$imagePath, basename($imagePath)));
        $taxonImage->setType($imageType);
        $this->imageUploader->upload($taxonImage);

        $taxon->addImage($taxonImage);

        $this->objectManager->flush($taxon);
    }

    /**
     * @Given /^the ("[^"]+" taxon) has children taxon "([^"]+)" and "([^"]+)"$/
     */
    public function theTaxonHasChildrenTaxonAnd(TaxonInterface $taxon, $firstTaxonName, $secondTaxonName)
    {
        $taxon->addChild($this->createTaxon($firstTaxonName));
        $taxon->addChild($this->createTaxon($secondTaxonName));

        $this->objectManager->flush($taxon);
    }

    /**
     * @param string $name
     *
     * @return TaxonInterface
     */
    private function createTaxon($name)
    {
        /** @var TaxonInterface $taxon */
        $taxon = $this->taxonFactory->createNew();
        $taxon->setName($name);
        $taxon->setCode(StringInflector::nameToCode($name));
        $taxon->setSlug($this->taxonSlugGenerator->generate($name));

        return $taxon;
    }

    /**
     * @param array $names
     *
     * @return TaxonInterface
     */
    private function createTaxonInManyLanguages(array $names)
    {
        /** @var TaxonInterface $taxon */
        $taxon = $this->taxonFactory->createNew();
        $taxon->setCode(StringInflector::nameToCode($names['en_US']));
        foreach ($names as $locale => $name) {
            /** @var TranslationInterface|TaxonTranslationInterface $taxonTranslation */
            $taxonTranslation = $this->taxonTranslationFactory->createNew();
            $taxonTranslation->setLocale($locale);
            $taxonTranslation->setName($name);
            $taxonTranslation->setSlug($this->taxonSlugGenerator->generate($name));

            $taxon->addTranslation($taxonTranslation);
        }

        return $taxon;
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
