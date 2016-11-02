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
use Behat\Mink\Exception\ElementNotFoundException;
use Sylius\Behat\Behaviour\SpecifiesItsCode;
use Sylius\Behat\Page\Admin\Crud\CreatePage as BaseCreatePage;
use Sylius\Component\Core\Model\TaxonInterface;
use Webmozart\Assert\Assert;

/**
 * @author Arkadiusz Krakowiak <arkadiusz.krakowiak@lakion.com>
 */
class CreatePage extends BaseCreatePage implements CreatePageInterface
{
    use SpecifiesItsCode;

    /**
     * {@inheritdoc}
     */
    public function countTaxons()
    {
        return count($this->getLeafs());
    }

    /**
     * {@inheritdoc}
     */
    public function countTaxonsByName($name)
    {
        $matchedLeafsCounter = 0;
        $leafs = $this->getLeafs();
        foreach ($leafs as $leaf) {
            if ($leaf->getText() === $name) {
                $matchedLeafsCounter++;
            }
        }

        return $matchedLeafsCounter;
    }

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
    public function deleteTaxonOnPageByName($name)
    {
        $leafs = $this->getLeafs();
        foreach ($leafs as $leaf) {
            if ($leaf->getText() === $name) {
                $leaf->getParent()->find('css', '.ui.red.button')->press();

                return;
            }
        }

        throw new ElementNotFoundException($this->getDriver(), 'Delete button');
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
    public function hasTaxonWithName($name)
    {
        return 0 !== $this->countTaxonsByName($name);
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
    public function attachImage($path, $code = null)
    {
        $filesPath = $this->getParameter('files_path');

        $this->getDocument()->find('css', '[data-form-collection="add"]')->click();

        $imageForm = $this->getLastImageElement();
        $imageForm->fillField('Code', $code);
        $imageForm->find('css', 'input[type="file"]')->attachFile($filesPath.$path);
    }

    /**
     * {@inheritDoc}
     */
    public function moveUp(TaxonInterface $taxon)
    {
        $this->moveLeaf($taxon, self::MOVE_DIRECTION_UP);
    }

    /**
     * {@inheritDoc}
     */
    public function moveDown(TaxonInterface $taxon)
    {
        $this->moveLeaf($taxon, self::MOVE_DIRECTION_DOWN);
    }

    /**
     * {@inheritDoc}
     */
    public function getFirstLeafName(TaxonInterface $parentTaxon = null)
    {
        return $this->getLeafs($parentTaxon)[0]->getText();
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
            'name' => '#sylius_taxon_translations_en_US_name',
            'parent' => '#sylius_taxon_parent',
            'permalink' => '#sylius_taxon_translations_en_US_permalink',
            'tree' => '.ui.list',
        ]);
    }

    /**
     * @param TaxonInterface $taxon
     * @param string $direction
     *
     * @throws ElementNotFoundException
     */
    private function moveLeaf(TaxonInterface $taxon, $direction)
    {
        Assert::oneOf($direction, [self::MOVE_DIRECTION_UP, self::MOVE_DIRECTION_DOWN]);

        $leafs = $this->getLeafs();
        foreach ($leafs as $leaf) {
            if ($leaf->getText() === $taxon->getName()) {
                $moveButton = $leaf->getParent()->find('css', sprintf('.%s', $direction));
                $moveButton->click();

                return;
            }
        }

        throw new ElementNotFoundException(
            $this->getDriver(),
            sprintf('Move %s button for %s taxon', $direction, $taxon->getName())
        );
    }

    /**
     * @param TaxonInterface|null $parentTaxon
     *
     * @return NodeElement[]
     *
     * @throws ElementNotFoundException
     */
    private function getLeafs(TaxonInterface $parentTaxon = null)
    {
        $tree = $this->getElement('tree');
        Assert::notNull($tree);
        /** @var NodeElement[] $leafs */
        $leafs = $tree->findAll('css', '.item > .content > .header');

        if (null === $parentTaxon) {
            return $leafs;
        }

        foreach ($leafs as $leaf) {
            if ($leaf->getText() === $parentTaxon->getName()) {
                return $leaf->findAll('css', '.item > .content > .header');
            }
        }
    }

    /**
     * @return NodeElement
     */
    private function getLastImageElement()
    {
        $images = $this->getElement('images');
        $items = $images->findAll('css', 'div[data-form-collection="item"]');

        Assert::notEmpty($items);

        return end($items);
    }
}
