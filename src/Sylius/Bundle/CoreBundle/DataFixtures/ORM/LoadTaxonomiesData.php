<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\CoreBundle\DataFixtures\ORM;

use Doctrine\Common\Persistence\ObjectManager;

/**
 * Default taxonomies to play with Sylius.
 *
 * @author Paweł Jędrzejewski <pjedrzejewski@diweb.pl>
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
     * Create and save taxonomy with given taxons.
     *
     * @param string $name
     * @param array  $taxons
     */
    private function createTaxonomy($name, array $taxons)
    {
        $taxonomy = $this
            ->getTaxonomyRepository()
            ->createNew()
        ;

        $taxonomy->setName($name);

        foreach ($taxons as $taxonName) {
            $taxon = $this
                ->getTaxonRepository()
                ->createNew()
            ;

            $taxon->setName($taxonName);

            $taxonomy->addTaxon($taxon);
            $this->setReference('Sylius.Taxon.'.$taxonName, $taxon);
        }

        $this->setReference('Sylius.Taxonomy.'.$name, $taxonomy);

        return $taxonomy;
    }

    public function getOrder()
    {
        return 5;
    }
}
