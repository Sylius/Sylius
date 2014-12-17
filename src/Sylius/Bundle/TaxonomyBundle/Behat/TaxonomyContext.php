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
            $this->thereIsTaxonomy($data['name'], false);
        }

        $this->getEntityManager()->flush();
    }

    /**
     * @Given /^I created taxonomy "([^""]*)"$/
     */
    public function thereIsTaxonomy($name, $flush = true)
    {
        $taxonomy = $this->getRepository('taxonomy')->createNew();
        $taxonomy->setName($name);

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

        $taxons = array();

        foreach ($taxonsTable->getRows() as $node) {
            $taxonList = explode('>', $node[0]);
            $parent = null;

            foreach ($taxonList as $taxonName) {
                $taxonName = trim($taxonName);

                if (!isset($taxons[$taxonName])) {
                    /* @var $taxon TaxonInterface */
                    $taxon = $this->getRepository('taxon')->createNew();
                    $taxon->setName($taxonName);

                    $taxons[$taxonName] = $taxon;
                }

                $taxon = $taxons[$taxonName];

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
     * @Given the following taxonomy translations exist
     */
    public function theFollowingTaxonomyTranslationsExist(TableNode $table)
    {
        $manager = $this->getEntityManager();

        foreach ($table->getHash() as $data) {
            $taxonomyTranslation = $this->findOneByName('taxonomy_translation', $data['taxonomy']);
            $taxonomy = $taxonomyTranslation->getTranslatable();
            $taxonomy
                ->setCurrentLocale($data['locale'])
                ->setName($data['name']);
        }

        $manager->flush();
    }

    /**
     * @Given the following taxon translations exist
     */
    public function theFollowingTaxonTranslationsExist(TableNode $table)
    {
        $manager = $this->getEntityManager();

        foreach ($table->getHash() as $data) {
            $taxonTranslation = $this->findOneByName('taxon_translation', $data['taxon']);
            $taxon = $taxonTranslation->getTranslatable();
            $taxon
                ->setCurrentLocale($data['locale'])
                ->setName($data['name']);
        }

        $manager->flush();
    }
}
