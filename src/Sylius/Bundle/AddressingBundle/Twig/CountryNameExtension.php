<?php

/*
 * This file is part of the Lakion package.
 *
 * (c) Lakion
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\AddressingBundle\Twig;

use Symfony\Component\Intl\Intl;

/**
 * @author Jan Góralski <jan.goralski@lakion.com>
 */
class CountryNameExtension extends \Twig_Extension
{
    /**
     * {@inheritdoc}
     */
    public function getFilters()
    {
        return array(
            'transCountryIso' => new \Twig_Filter_Method($this, 'translateIsoName'),
        );
    }

    /**
     * @param string $isoName
     * @param string $locale
     *
     * @return string
     */
    public function translateIsoName($isoName, $locale = null)
    {
        return Intl::getRegionBundle()->getCountryName($isoName, $locale);
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'sylius_country_name';
    }
}
