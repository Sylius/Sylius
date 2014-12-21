<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\LocaleBundle\Provider;

use Sylius\Component\Locale\Provider\LocaleProvider as BaseLocaleProvider;

/**
 * @author Gonzalo Vilaseca <gvilaseca@reiss.co.uk>
 */
class LocaleProvider extends BaseLocaleProvider implements LocaleProviderInterface
{
    /**
     * Meta data for special languages,
     * by default we assume that all languages are LTR except mentioned otherwise.
     *
     * @var array
     */
    protected static $localeMetaData = array(
        'fa' => array(
            'rtl' => true,
            'calendar' => 'persian'
        ),
        'fa_IR' => array(
            'rtl' => true,
            'calendar' => 'persian'
        ),
        'fa_AF' => array(
            'rtl' => true,
            'calendar' => 'persian'
        ),
        'ar' => array(
            'rtl' => true,
            'calendar' => 'islamic'
        ),
        'ckb' => array(
            'rtl' => true
        ),
        'he_IL' => array(
            'rtl' => true
        ),
        'ug_CN' => array(
            'rtl' => true
        ),
        'dv' => array(
            'rtl' => true
        ),
        'ha' => array(
            'rtl' => true
        ),
        'ps' => array(
            'rtl' => true
        ),
        'uz_UZ' => array(
            'rtl' => true
        ),
        'yi' => array(
            'rtl' => true
        )
    );
}