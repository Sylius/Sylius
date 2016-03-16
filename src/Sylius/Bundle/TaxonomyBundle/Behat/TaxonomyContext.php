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
     * @Given /^there are following taxonomies defined:$/
     */
    public function thereAreFollowingTaxonomies(TableNode $table)
    {
        foreach ($table->getHash() as $data) {
            $this->thereIsTaxonomy($data['name'], $data['code'], false);
        }

        $this->getEntityManager()->flush();
    }

    /**
     * @Given /^I created taxonomy "([^""]*)" with code "([^""]*)"$/
     */
    public function thereIsTaxonomy($name, $code, $flush = true)
    {
        $taxonomy = $this->getFactory('taxonomy')->createNew();
        $taxonomy->getRoot()->setCode($code);
        $taxonomy->setName($name);

        if (null === $taxonomy->getCurrentLocale()) {
            $taxonomy->setCurrentLocale('en_US');
        }

        $this->getEntityManager()->persist($taxonomy);
        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    /**
     * @Given /^taxonomy "([^""]*)" has following taxons:$/
     */
    public function taxonomyHasFollowingTaxons($taxonomyName, TableNode $taxonsTable)
    {
        $taxonomy = $this->findOneByName('taxonomy', $taxonomyName);
        $manager = $this->getEntityManager();

        $taxons = [];

        foreach ($taxonsTable->getRows() as $node) {
            $taxonList = explode('>', $node[0]);
            $parent = null;

            foreach ($taxonList as $taxon) {
                $taxon = trim($taxon);
                $taxonData = preg_split('[\\[|\\]]', $taxon, -1, PREG_SPLIT_NO_EMPTY);

                if (!isset($taxons[$taxonData[0]])) {
                    /* @var $taxon TaxonInterface */
                    $taxon = $this->getFactory('taxon')->createNew();
                    $taxon->setName($taxonData[0]);
                    $taxon->setCode($taxonData[1]);

                    $taxons[$taxonData[0]] = $taxon;
                }

                $taxon = $taxons[$taxonData[0]];

                if (null !== $parent) {
                    $parent->addChild($taxon);
                } else {
                    $taxonomy->addTaxon($taxon);
                }

                $parent = $taxon;
            }
        }

        $manager->persist($taxonomy);
        $manager->flush();
    }

    /**
     * @Given the following taxonomy translations exist:
     */
    public function theFollowingTaxonomyTranslationsExist(TableNode $table)
    {
        $manager = $this->getEntityManager();

        foreach ($table->getHash() as $data) {
            $taxonomyTranslation = $this->findOneByName('taxonomy_translation', $data['taxonomy']);

            $taxonomy = $taxonomyTranslation->getTranslatable();
            $taxonomy->setCurrentLocale($data['locale']);
            $taxonomy->setFallbackLocale($data['locale']);

            $taxonomy->setName($data['name']);
        }

        $manager->flush();
    }

    /**
     * @Given the following taxon translations exist:
     */
    public function theFollowingTaxonTranslationsExist(TableNode $table)
    {
        foreach ($table->getHash() as $data) {
            $taxonTranslation = $this->findOneByName('taxon_translation', $data['taxon']);

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
