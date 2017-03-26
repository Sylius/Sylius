<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Behat\Page\Admin\Taxon;

use Behat\Mink\Driver\Selenium2Driver;
use Behat\Mink\Element\NodeElement;
use Behat\Mink\Exception\ElementNotFoundException;
use Sylius\Behat\Behaviour\ChecksCodeImmutability;
use Sylius\Behat\Page\Admin\Crud\UpdatePage as BaseUpdatePage;
use Sylius\Behat\Service\AutocompleteHelper;
use Sylius\Behat\Service\SlugGenerationHelper;
use Sylius\Component\Core\Model\TaxonInterface;
use Webmozart\Assert\Assert;

/**
 * @author Arkadiusz Krakowiak <arkadiusz.krakowiak@lakion.com>
 */
class UpdatePage extends BaseUpdatePage implements UpdatePageInterface
{
    use ChecksCodeImmutability;

    /**
     * {@inheritdoc}
     */
    public function chooseParent(TaxonInterface $taxon)
    {
        AutocompleteHelper::chooseValue($this->getSession(), $this->getElement('parent')->getParent(), $taxon->getName());
    }

    /**
     * {@inheritdoc}
     */
    public function describeItAs($description, $languageCode)
    {
        $this->getDocument()->fillField(sprintf('sylius_taxon_translations_%s_description', $languageCode), $description);
    }

    /**
     * {@inheritdoc}
     */
    public function nameIt($name, $languageCode)
    {
        $this->activateLanguageTab($languageCode);
        $this->getDocument()->fillField(sprintf('sylius_taxon_translations_%s_name', $languageCode), $name);

        if ($this->getDriver() instanceof Selenium2Driver) {
            SlugGenerationHelper::waitForSlugGeneration(
                $this->getSession(),
                $this->getElement('slug', ['%language%' => $languageCode])
            );
        }
    }

    /**
     * {@inheritdoc}
     */
    public function specifySlug($slug, $languageCode)
    {
        $this->getDocument()->fillField(sprintf('sylius_taxon_translations_%s_slug', $languageCode), $slug);
    }

    /**
     * {@inheritdoc}
     */
    public function attachImage($path, $type = null)
    {
        $filesPath = $this->getParameter('files_path');

        $this->getDocument()->find('css', '[data-form-collection="add"]')->click();

        $imageForm = $this->getLastImageElement();
        if (null !== $type) {
            $imageForm->fillField('Type', $type);
        }

        $imageForm->find('css', 'input[type="file"]')->attachFile($filesPath.$path);
    }

    /**
     * {@inheritdoc}
     */
    public function isImageWithTypeDisplayed($type)
    {
        $imageElement = $this->getImageElementByType($type);

        if (null === $imageElement) {
            return false;
        }

        $imageUrl = $imageElement->find('css', 'img')->getAttribute('src');
        $this->getDriver()->visit($imageUrl);
        $pageText = $this->getDocument()->getText();
        $this->getDriver()->back();

        return false === stripos($pageText, '404 Not Found');
    }

    /**
     * {@inheritdoc}
     */
    public function isSlugReadonly($languageCode = 'en_US')
    {
        return SlugGenerationHelper::isSlugReadonly(
            $this->getSession(),
            $this->getElement('slug', ['%language%' => $languageCode])
        );
    }

    /**
     * {@inheritdoc}
     */
    public function removeImageWithType($type)
    {
        $imageElement = $this->getImageElementByType($type);
        $imageElement->clickLink('Delete');
    }

    public function removeFirstImage()
    {
        $imageElement = $this->getFirstImageElement();
        $imageElement->clickLink('Delete');
    }

    /**
     * {@inheritdoc}
     */
    public function enableSlugModification($languageCode = 'en_US')
    {
        SlugGenerationHelper::enableSlugModification(
            $this->getSession(),
            $this->getElement('toggle_taxon_slug_modification_button', ['%locale%' => $languageCode])
        );
    }

    /**
     * {@inheritdoc}
     */
    public function countImages()
    {
        $imageElements = $this->getImageElements();

        return count($imageElements);
    }

    /**
     * {@inheritdoc}
     */
    public function changeImageWithType($type, $path)
    {
        $filesPath = $this->getParameter('files_path');

        $imageForm = $this->getImageElementByType($type);
        $imageForm->find('css', 'input[type="file"]')->attachFile($filesPath.$path);
    }

    /**
     * {@inheritdoc}
     */
    public function modifyFirstImageType($type)
    {
        $firstImage = $this->getFirstImageElement();

        $typeField = $firstImage->findField('Type');
        $typeField->setValue($type);
    }

    /**
     * {@inheritdoc}
     */
    public function getParent()
    {
        return $this->getElement('parent')->getValue();
    }

    /**
     * {@inheritdoc}
     */
    public function getSlug($languageCode = 'en_US')
    {
        return $this->getElement('slug', ['%language%' => $languageCode])->getValue();
    }

    /**
     * {@inheritdoc}
     */
    public function getValidationMessageForImage()
    {
        $lastImageElement = $this->getLastImageElement();

        $foundElement = $lastImageElement->find('css', '.sylius-validation-error');
        if (null === $foundElement) {
            throw new ElementNotFoundException($this->getSession(), 'Tag', 'css', '.sylius-validation-error');
        }

        return $foundElement->getText();
    }

    /**
     * {@inheritdoc}
     */
    public function getValidationMessageForImageAtPlace($place)
    {
        $images = $this->getImageElements();

        $foundElement = $images[$place]->find('css', '.sylius-validation-error');
        if (null === $foundElement) {
            throw new ElementNotFoundException($this->getSession(), 'Tag', 'css', '.sylius-validation-error');
        }

        return $foundElement->getText();
    }

    /**
     * {@inheritdoc}
     */
    public function activateLanguageTab($locale)
    {
        if (!$this->getDriver() instanceof Selenium2Driver) {
            return;
        }

        $languageTabTitle = $this->getElement('language_tab', ['%locale%' => $locale]);
        if (!$languageTabTitle->hasClass('active')) {
            $languageTabTitle->click();
        }

        $this->getDocument()->waitFor(10, function () use ($languageTabTitle) {
            return $languageTabTitle->hasClass('active');
        });
    }

    /**
     * {@inheritdoc}
     */
    protected function getElement($name, array $parameters = [])
    {
        if (!isset($parameters['%language%'])) {
            $parameters['%language%'] = 'en_US';
        }

        return parent::getElement($name, $parameters);
    }

    /**
     * @return NodeElement
     */
    protected function getCodeElement()
    {
        return $this->getElement('code');
    }

    /**
     * {@inheritdoc}
     */
    protected function getDefinedElements()
    {
        return array_merge(parent::getDefinedElements(), [
            'code' => '#sylius_taxon_code',
            'description' => '#sylius_taxon_translations_en_US_description',
            'images' => '#sylius_taxon_images',
            'language_tab' => '[data-locale="%locale%"] .title',
            'name' => '#sylius_taxon_translations_en_US_name',
            'parent' => '#sylius_taxon_parent',
            'slug' => '#sylius_taxon_translations_%language%_slug',
            'toggle_taxon_slug_modification_button' => '[data-locale="%locale%"] .toggle-taxon-slug-modification',
        ]);
    }

    /**
     * @return NodeElement
     */
    private function getLastImageElement()
    {
        $imageElements = $this->getImageElements();

        Assert::notEmpty($imageElements);

        return end($imageElements);
    }

    /**
     * @return NodeElement
     */
    private function getFirstImageElement()
    {
        $imageElements = $this->getImageElements();

        Assert::notEmpty($imageElements);

        return reset($imageElements);
    }

    /**
     * @return NodeElement[]
     */
    private function getImageElements()
    {
        $images = $this->getElement('images');

        return $images->findAll('css', 'div[data-form-collection="item"]');
    }

    /**
     * @param string $type
     *
     * @return NodeElement
     */
    private function getImageElementByType($type)
    {
        $images = $this->getElement('images');
        $typeInput = $images->find('css', 'input[value="'.$type.'"]');

        if (null === $typeInput) {
            return null;
        }

        return $typeInput->getParent()->getParent()->getParent();
    }
}
