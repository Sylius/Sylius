<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Sylius\Behat\Context\Setup;

use Behat\Behat\Context\Context;
use Behat\Mink\Element\NodeElement;
use Doctrine\Persistence\ObjectManager;
use Sylius\Component\Core\Formatter\StringInflector;
use Sylius\Component\Core\Model\ImageInterface;
use Sylius\Component\Core\Model\TaxonInterface;
use Sylius\Component\Core\Uploader\ImageUploaderInterface;
use Sylius\Component\Resource\Factory\FactoryInterface;
use Sylius\Component\Resource\Repository\RepositoryInterface;
use Sylius\Component\Taxonomy\Generator\TaxonSlugGeneratorInterface;
use Sylius\Component\Taxonomy\Model\TaxonTranslationInterface;
use Symfony\Component\HttpFoundation\File\UploadedFile;

final class TaxonomyContext implements Context
{
    public function __construct(
        private RepositoryInterface $taxonRepository,
        private FactoryInterface $taxonFactory,
        private FactoryInterface $taxonTranslationFactory,
        private FactoryInterface $taxonImageFactory,
        private ObjectManager $objectManager,
        private ImageUploaderInterface $imageUploader,
        private TaxonSlugGeneratorInterface $taxonSlugGenerator,
        private \ArrayAccess $minkParameters,
    ) {
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
        $taxonImage->setFile(new UploadedFile($filesPath . $imagePath, basename($imagePath)));
        $taxonImage->setType($imageType);
        $this->imageUploader->upload($taxonImage);

        $taxon->addImage($taxonImage);

        $this->objectManager->persist($taxon);
        $this->objectManager->flush();
    }

    /**
     * @Given /^the ("[^"]+" taxon) has child taxon "([^"]+)"$/
     * @Given /^the ("[^"]+" taxon) has children taxon "([^"]+)" and "([^"]+)"$/
     * @Given /^the ("[^"]+" taxon) has children taxons "([^"]+)" and "([^"]+)"$/
     * @Given /^the ("[^"]+" taxon) has children taxons "([^"]+)", "([^"]+)" and "([^"]+)"$/
     */
    public function theTaxonHasChildrenTaxonAnd(TaxonInterface $taxon, string ...$taxonsNames): void
    {
        foreach ($taxonsNames as $taxonName) {
            $taxon->addChild($this->createTaxon($taxonName));
        }

        $this->objectManager->persist($taxon);
        $this->objectManager->flush();
    }

    /**
     * @Given /^the ("[^"]+" taxon)(?:| also) is enabled/
     */
    public function theTaxonIsEnabled(TaxonInterface $taxon): void
    {
        $taxon->setEnabled(true);

        $this->objectManager->flush();
    }

    /**
     * @Given /^the ("[^"]+" taxon)(?:| also) is disabled$/
     */
    public function theTaxonIsDisabled(TaxonInterface $taxon): void
    {
        $taxon->setEnabled(false);

        $this->objectManager->flush();
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
        $taxon->setCode(StringInflector::nameToLowercaseCode($name));
        $taxon->setSlug($this->taxonSlugGenerator->generate($taxon));

        return $taxon;
    }

    /**
     * @return TaxonInterface
     */
    private function createTaxonInManyLanguages(array $names)
    {
        /** @var TaxonInterface $taxon */
        $taxon = $this->taxonFactory->createNew();
        $taxon->setCode(StringInflector::nameToCode($names['en_US']));
        foreach ($names as $locale => $name) {
            /** @var TaxonTranslationInterface $taxonTranslation */
            $taxonTranslation = $this->taxonTranslationFactory->createNew();
            $taxonTranslation->setLocale($locale);
            $taxonTranslation->setName($name);

            $taxon->addTranslation($taxonTranslation);

            $taxonTranslation->setSlug($this->taxonSlugGenerator->generate($taxon, $locale));
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
        return $this->minkParameters[$name] ?? null;
    }
}
