<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Sylius Sp. z o.o.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Sylius\Behat\Context\Setup;

use Behat\Behat\Context\Context;
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
     * @Given /^the ("[^"]+" taxon) has child taxon "([^"]+)" in many locales$/
     */
    public function theTaxonHasChildrenTaxonsInManyLocales(TaxonInterface $taxon, string $childTaxonName): void
    {
        $translationMap = [
            'en_US' => $childTaxonName,
            'fr_FR' => $childTaxonName . '_FR',
            'de_DE' => $childTaxonName . '_DE',
            'es_ES' => $childTaxonName . '_ES',
            'pl_PL' => $childTaxonName . '_PL',
            'pt_PT' => $childTaxonName . '_PT',
            'uk_UA' => $childTaxonName . '_UA',
            'cn_CN' => $childTaxonName . '_CN',
            'ja_JP' => $childTaxonName . '_JP',
            'bg_BG' => $childTaxonName . '_BG',
            'da_DK' => $childTaxonName . '_DK',
        ];

        $taxon->addChild($this->createTaxonInManyLanguages($translationMap));

        $this->objectManager->persist($taxon);
        $this->objectManager->flush();
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
            $taxon->addChild($this->createChildTaxon($taxonName, $taxon));
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

    private function createTaxon(string $name): TaxonInterface
    {
        /** @var TaxonInterface $taxon */
        $taxon = $this->taxonFactory->createNew();
        $taxon->setName($name);
        $taxon->setCode(StringInflector::nameToLowercaseCode($name));
        $taxon->setSlug($this->taxonSlugGenerator->generate($taxon));

        return $taxon;
    }

    private function createChildTaxon(string $name, TaxonInterface $parent): TaxonInterface
    {
        $child = $this->createTaxon($name);
        $child->setParent($parent);
        $child->setSlug($this->taxonSlugGenerator->generate($child));

        return $child;
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

    private function getParameter(string $name): ?string
    {
        return $this->minkParameters[$name] ?? null;
    }
}
