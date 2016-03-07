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

/**
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 * @author Gonzalo Vilaseca <gvilaseca@reiss.co.uk>
 */
class LoadTaxonsData extends DataFixture
{
    /**
     * {@inheritdoc}
     */
    public function load(ObjectManager $manager)
    {
        $manager->persist($this->createTaxons(
            'CATEGORY', [$this->defaultLocale => 'Category', 'es_ES' => 'Categoria'],
            [
                ['code' => 'TX1', 'locales' => [$this->defaultLocale => 'T-Shirts', 'es_ES' => 'Camisetas']],
                ['code' => 'TX2', 'locales' => [$this->defaultLocale => 'Stickers', 'es_ES' => 'Pegatinas']],
                ['code' => 'TX3', 'locales' => [$this->defaultLocale => 'Mugs', 'es_ES' => 'Tazas']],
                ['code' => 'TX4', 'locales' => [$this->defaultLocale => 'Books', 'es_ES' => 'Libros']],
            ]));

        $manager->persist($this->createTaxons(
            'BRAND', [$this->defaultLocale => 'Brand', 'es_ES' => 'Marca'],
            [
                ['code' => 'TX5', 'locales' => [$this->defaultLocale => 'SuperTees', 'es_ES' => 'SuperCamisetas']],
                ['code' => 'TX6', 'locales' => [$this->defaultLocale => 'Stickypicky', 'es_ES' => 'Pegapicky']],
                ['code' => 'TX7', 'locales' => [$this->defaultLocale => 'Mugland', 'es_ES' => 'Mundotaza']],
                ['code' => 'TX8', 'locales' => [$this->defaultLocale => 'Bookmania', 'es_ES' => 'Libromania']],
            ]));

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
     * Create and save taxon with given taxons.
     *
     * @param string $code
     * @param array $rootTaxonName
     * @param array $childrenArray
     *
     * @return TaxonInterface
     */
    protected function createTaxons($code, array $rootTaxonName, array $childrenArray)
    {
        /* @var $rootTaxon TaxonInterface */
        $rootTaxon = $this->getTaxonFactory()->createNew();
        $rootTaxon->setCode($code);

        foreach ($rootTaxonName as $locale => $name) {
            $rootTaxon->setCurrentLocale($locale);
            $rootTaxon->setFallbackLocale($locale);
            $rootTaxon->setName($name);

            if ($this->defaultLocale === $locale) {
                $this->setReference('Sylius.Taxon.'.$name, $rootTaxon);
            }
        }

        foreach ($childrenArray as $taxonArray) {
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

            $rootTaxon->addChild($taxon);
        }

        return $rootTaxon;
    }
}
