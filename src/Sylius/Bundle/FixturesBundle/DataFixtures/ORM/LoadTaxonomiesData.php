<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\FixturesBundle\DataFixtures\ORM;

use Doctrine\Common\Persistence\ObjectManager;
use Sylius\Bundle\FixturesBundle\DataFixtures\DataFixture;
use Sylius\Component\Taxonomy\Model\TaxonInterface;
use Sylius\Component\Taxonomy\Model\TaxonomyInterface;

/**
 * Default taxonomies to play with Sylius.
 *
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 */
class LoadTaxonomiesData extends DataFixture
{
    /**
     * {@inheritdoc}
     */
    public function load(ObjectManager $manager)
    {
        $manager->persist($this->createTaxonomy('Category', array(
            'T-Shirts', 'Stickers', 'Mugs', 'Books'
        )));

        $manager->persist($this->createTaxonomy('Brand', array(
            'SuperTees', 'Stickypicky', 'Mugland', 'Bookmania'
        )));

        $manager->flush();
    }

    /**
     * {@inheritdoc}
     */
    public function getOrder()
    {
        return 5;
    }

    /**
     * Create and save taxonomy with given taxons.
     *
     * @param string $name
     * @param array  $taxons
     *
     * @return TaxonomyInterface
     */
    protected function createTaxonomy($name, array $taxons)
    {
        /* @var $taxonomy TaxonomyInterface */
        $taxonomy = $this->getTaxonomyRepository()->createNew();
        $taxonomy->setName($name);

        foreach ($taxons as $taxonName) {
            /* @var $taxon TaxonInterface */
            $taxon = $this->getTaxonRepository()->createNew();
            $taxon->setName($taxonName);

            $taxonomy->addTaxon($taxon);
            $this->setReference('Sylius.Taxon.'.$taxonName, $taxon);
        }

        $this->setReference('Sylius.Taxonomy.'.$name, $taxonomy);

        return $taxonomy;
    }
}
