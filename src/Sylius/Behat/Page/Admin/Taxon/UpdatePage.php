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

use Behat\Mink\Element\NodeElement;
use Sylius\Behat\Behaviour\ChecksCodeImmutability;
use Sylius\Behat\Page\Admin\Crud\UpdatePage as BaseUpdatePage;
use Sylius\Component\Core\Model\TaxonInterface;

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
        $this->getElement('parent')->selectOption($taxon->getName(), false);
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
        $this->getDocument()->fillField(sprintf('sylius_taxon_translations_%s_name', $languageCode), $name);
    }

    /**
     * {@inheritdoc}
     */
    public function specifyPermalink($permalink, $languageCode)
    {
        $this->getDocument()->fillField(sprintf('sylius_taxon_translations_%s_permalink', $languageCode), $permalink);
    }

    /**
     * {@inheritdoc}
     */
    public function attachImageWithCode($code, $path)
    {
        $filesPath = $this->getParameter('files_path');

        $this->getDocument()->clickLink('Add');

        $imageForm = $this->getLastImageElement();
        $imageForm->fillField('Code', $code);
        $imageForm->find('css', 'input[type="file"]')->attachFile($filesPath.$path);
    }

    /**
     * {@inheritdoc}
     */
    public function isImageWithCodeDisplayed($code)
    {
        $imageElement = $this->getImageElementByCode($code);

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
    public function removeImageWithCode($code)
    {
        $imageElement = $this->getImageElementByCode($code);
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
    public function countImages()
    {
        $imageElements = $this->getImageElements();

        return count($imageElements);
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
            'images' => '#sylius_taxon_images',
            'name' => '#sylius_taxon_translations_en_US_name',
            'parent' => '#sylius_taxon_parent',
            'permalink' => '#sylius_taxon_translations_en_US_permalink',
            'description' => '#sylius_taxon_translations_en_US_description',
        ]);
    }

    /**
     * @return NodeElement
     */
    private function getLastImageElement()
    {
        $imageElements = $this->getImageElements();

        return end($imageElements);
    }

    /**
     * @return NodeElement
     */
    private function getFirstImageElement()
    {
        $imageElements = $this->getImageElements();

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
     * @param string $code
     *
     * @return NodeElement
     */
    private function getImageElementByCode($code)
    {
        $images = $this->getElement('images');
        $inputCode = $images->find('css', 'input[value="'.$code.'"]');

        if (null === $inputCode) {
            return null;
        }

        return $inputCode->getParent()->getParent()->getParent();
    }
}
