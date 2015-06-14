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
        $manager->persist($this->createTaxonomy(
            array($this->defaultLocale => 'Category', 'es_ES' => 'Categoria'),
            array(
                array($this->defaultLocale => 'T-Shirts', 'es_ES' => 'Camisetas'),
                array($this->defaultLocale => 'Stickers', 'es_ES' => 'Pegatinas'),
                array($this->defaultLocale => 'Mugs', 'es_ES' => 'Tazas'),
                array($this->defaultLocale => 'Books', 'es_ES' => 'Libros'),
            )));

        $manager->persist($this->createTaxonomy(
            array($this->defaultLocale => 'Brand', 'es_ES' => 'Marca'),
            array(
                array($this->defaultLocale => 'SuperTees', 'es_ES' => 'SuperCamisetas'),
                array($this->defaultLocale => 'Stickypicky', 'es_ES' => 'Pegapicky'),
                array($this->defaultLocale => 'Mugland', 'es_ES' => 'Mundotaza'),
                array($this->defaultLocale => 'Bookmania', 'es_ES' => 'Libromania'),
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
     * @param array $taxonomyName
     * @param array $taxonsArray
     *
     * @internal param string $name
     * @internal param array $taxons
     *
     * @return TaxonomyInterface
     */
    protected function createTaxonomy(array $taxonomyName, array $taxonsArray)
    {
        /* @var $taxonomy TaxonomyInterface */
        $taxonomy = $this->getTaxonomyRepository()->createNew();

        foreach ($taxonomyName as $locale => $name) {
            $taxonomy->setCurrentLocale($locale);
            $taxonomy->setFallbackLocale($locale);
            $taxonomy->setName($name);

            if ($this->defaultLocale === $locale) {
                $this->setReference('Sylius.Taxonomy.'.$name, $taxonomy);
            }
        }

        foreach ($taxonsArray as $taxonArray) {
            /* @var $taxon TaxonInterface */
            $taxon = $this->getTaxonRepository()->createNew();
            foreach ($taxonArray as $locale => $taxonName) {
                $taxon->setCurrentLocale($locale);
                $taxon->setFallbackLocale($locale);
                $taxon->setName($taxonName);
                $taxon->setDescription($this->fakers[$locale]->paragraph);

                if ($this->defaultLocale === $locale) {
                    $this->setReference('Sylius.Taxon.'.$taxonName, $taxon);
                }
            }
            $taxonomy->addTaxon($taxon);
        }

        return $taxonomy;
    }
}
