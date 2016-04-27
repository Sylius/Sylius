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
                $leaf->getParent()->pressButton('Delete');

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
    protected function getDefinedElements()
    {
        return array_merge(parent::getDefinedElements(), [
            'code' => '#sylius_taxon_code',
            'name' => '#sylius_taxon_translations_en_US_name',
            'parent' => '#sylius_taxon_parent',
            'permalink' => '#sylius_taxon_translations_en_US_permalink',
            'description' => '#sylius_taxon_translations_en_US_description',
            'tree' => '.ui.list',
        ]);
    }

    /**
     * @return NodeElement[]
     *
     * @throws ElementNotFoundException
     */
    private function getLeafs()
    {
        $tree = $this->getElement('tree');
        Assert::notNull($tree);

        return $tree->findAll('css', '.item > .content > .header');
    }
}
