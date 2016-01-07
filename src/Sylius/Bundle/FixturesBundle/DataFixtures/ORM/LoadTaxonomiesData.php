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
            'RTX1', array($this->defaultLocale => 'Category', 'es_ES' => 'Categoria'),
            array(
                array('code' => 'TX1', 'locales' => array($this->defaultLocale => 'T-Shirts', 'es_ES' => 'Camisetas')),
                array('code' => 'TX2', 'locales' => array($this->defaultLocale => 'Stickers', 'es_ES' => 'Pegatinas')),
                array('code' => 'TX3', 'locales' => array($this->defaultLocale => 'Mugs', 'es_ES' => 'Tazas')),
                array('code' => 'TX4', 'locales' => array($this->defaultLocale => 'Books', 'es_ES' => 'Libros')),
            )));

        $manager->persist($this->createTaxonomy(
            'RTX2', array($this->defaultLocale => 'Brand', 'es_ES' => 'Marca'),
            array(
                array('code' => 'TX5', 'locales' => array($this->defaultLocale => 'SuperTees', 'es_ES' => 'SuperCamisetas')),
                array('code' => 'TX6', 'locales' => array($this->defaultLocale => 'Stickypicky', 'es_ES' => 'Pegapicky')),
                array('code' => 'TX7', 'locales' => array($this->defaultLocale => 'Mugland', 'es_ES' => 'Mundotaza')),
                array('code' => 'TX8', 'locales' => array($this->defaultLocale => 'Bookmania', 'es_ES' => 'Libromania')),
            )));

        $manager->flush();
    }

    /**
     * {@inheritdoc}
     */
    public function getOrder()
    {
        return 10;
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
    protected function createTaxonomy($code, array $taxonomyName, array $taxonsArray)
    {
        /* @var $taxonomy TaxonomyInterface */
        $taxonomy = $this->getTaxonomyFactory()->createNew();
        $taxonomy->getRoot()->setCode($code);

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
            $taxon = $this->getTaxonFactory()->createNew();
            $taxon->setCode($taxonArray['code']);
            foreach ($taxonArray['locales'] as $locale => $taxonName) {
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
