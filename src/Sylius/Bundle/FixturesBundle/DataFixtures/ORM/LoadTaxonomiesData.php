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
 * @author Gonzalo Vilaseca <gvilaseca@reiss.co.uk>
 */
class LoadTaxonomiesData extends DataFixture
{
    /**
     * {@inheritdoc}
     */
    public function load(ObjectManager $manager)
    {
        //TODO 'en' pillar parametro x defecto
        $manager->persist($this->createTaxonomy(
            array('en' => 'Category', 'es' => 'Categoria'),
            array(
                array('en' => 'T-Shirts', 'es' => 'Camisetas'),
                array('en' => 'Stickers', 'es' => 'Pegatinas'),
                array('en' => 'Mugs', 'es' => 'Tazas'),
                array('en' => 'Books', 'es' => 'Libros'),
            )));

        $manager->persist($this->createTaxonomy(
            array('en' => 'Brand', 'es' => 'Marca'),
            array(
                array('en' => 'SuperTees', 'es' => 'SuperCamisetas'),
                array('en' => 'Stickypicky', 'es' => 'Pegapicky'),
                array('en' => 'Mugland', 'es' => 'Mundotaza'),
                array('en' => 'Bookmania', 'es' => 'Lbromania'),
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
    protected function createTaxonomy(array $taxonomyName, array $taxonsArray)
    {
        /* @var $taxonomy TaxonomyInterface */
        $taxonomy = $this->getTaxonomyRepository()->createNew();

        foreach ($taxonomyName as $locale => $name) {
            $taxonomy->setCurrentLocale($locale);
            $taxonomy->setName($name);
            if ('en' == $locale) {
                $this->setReference('Sylius.Taxonomy.' . $name, $taxonomy);
            }
        }

        foreach ($taxonsArray as $taxonArray) {
            /* @var $taxon TaxonInterface */
            $taxon = $this->getTaxonRepository()->createNew();
            foreach ($taxonArray as $locale => $taxonName) {
                $taxon->setCurrentLocale($locale);
                $taxon->setName($taxonName);


                if ('en' == $locale) {
                    $this->setReference('Sylius.Taxon.' . $taxonName, $taxon);
                }
            }
            $taxonomy->addTaxon($taxon);
        }

        return $taxonomy;
    }
}
