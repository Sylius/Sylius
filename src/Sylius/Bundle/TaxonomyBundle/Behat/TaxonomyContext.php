<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\TaxonomyBundle\Behat;

use Behat\Gherkin\Node\TableNode;
use Sylius\Bundle\ResourceBundle\Behat\DefaultContext;
use Sylius\Component\Taxonomy\Model\TaxonInterface;

class TaxonomyContext extends DefaultContext
{
    /**
     * @Given /^there are following taxons defined:$/
     */
    public function thereAreFollowingTaxons(TableNode $table)
    {
        foreach ($table->getHash() as $data) {
            $this->thereIsTaxon($data['name'], $data['code'], false);
        }

        $this->getEntityManager()->flush();
    }

    /**
     * @Given /^I created taxon "([^""]*)" with code "([^""]*)"$/
     */
    public function thereIsTaxon($name, $code, $flush = true)
    {
        /** @var TaxonInterface $taxon */
        $taxon = $this->getFactory('taxon')->createNew();
        $taxon->setName($name);
        $taxon->setCode($code);

        $this->getEntityManager()->persist($taxon);
        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    /**
     * @Given /^taxon "([^""]*)" has following children:$/
     */
    public function taxonHasFollowingChildren($taxonName, TableNode $childrenTable)
    {
        /* @var $taxon TaxonInterface */
        $taxon = $this->findOneByName('taxon', $taxonName);
        $manager = $this->getEntityManager();

        $children = [];

        foreach ($childrenTable->getRows() as $node) {
            $taxonList = explode('>', $node[0]);

            /* @var $parent TaxonInterface */
            $parent = null;

            foreach ($taxonList as $item) {
                $item = trim($item);
                $childData = preg_split('[\\[|\\]]', $item, -1, PREG_SPLIT_NO_EMPTY);

                if (!isset($children[$childData[0]])) {
                    /* @var $child TaxonInterface */
                    $child = $this->getFactory('taxon')->createNew();
                    $child->setName($childData[0]);
                    $child->setCode($childData[1]);

                    $children[$childData[0]] = $child;
                }

                $child = $children[$childData[0]];

                if (null !== $parent) {
                    $parent->addChild($child);
                } else {
                    $taxon->addChild($child);
                }

                $parent = $child;
            }
        }

        $manager->persist($taxon);
        $manager->flush();
    }

    /**
     * @Given the following taxon translations exist:
     */
    public function theFollowingTaxonTranslationsExist(TableNode $table)
    {
        foreach ($table->getHash() as $data) {
            $taxonTranslation = $this->findOneByName('taxon_translation', $data['taxon']);

            /** @var TaxonInterface $taxon */
            $taxon = $taxonTranslation->getTranslatable();
            $taxon->setCurrentLocale($data['locale']);
            $taxon->setFallbackLocale($data['locale']);

            $taxon->setName($data['name']);
        }

        $this->getEntityManager()->flush();
    }

    /**
     * @Then taxon translation :taxonName should have permalink :expectedPermalink
     */
    public function taxonForLocaleShouldHavePermalink($taxonName, $expectedPermalink)
    {
        $taxonTranslation = $this->findOneByName('taxon_translation', $taxonName);
        $permalink = $taxonTranslation->getPermalink();

        \PHPUnit_Framework_Assert::assertEquals($expectedPermalink, $permalink);
    }

    /**
     * @When I change then name of taxon translation :taxonName to :newName
     */
    public function iChangeThenNameOfTaxonTranslationTo($taxonName, $newName)
    {
        $taxonTranslation = $this->findOneByName('taxon_translation', $taxonName);
        $taxonTranslation->setName($newName);

        $this->getEntityManager()->flush();
    }
}
