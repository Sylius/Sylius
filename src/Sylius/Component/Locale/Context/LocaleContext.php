<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Component\Locale\Context;

use Sylius\Component\Storage\StorageInterface;

/**
 * Default locale context implementation.
 *
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 * @author Joseph Bielawski <stloyd@gmail.com>
 * @author Aram Alipoor <aram.alipoor@gmail.com>
 */
class LocaleContext implements LocaleContextInterface
{
    /**
     * Default locale.
     *
     * @var string
     */
    protected $defaultLocale;

    /**
     * Locale storage.
     *
     * @var StorageInterface
     */
    protected $storage;

    /**
     * Meta data for special languages,
     * by default we assume that all languages are LTR except mentioned otherwise.
     *
     * TODO This list should be completed
     *
     * @var array
     */
    protected static $localeMetaData = array(
        'fa' => array(
            'direction' => 'rtl',
            'calendar' => 'persian'
        ),
        'fa_IR' => array(
            'direction' => 'rtl',
            'calendar' => 'persian'
        ),
        'fa_AF' => array(
            'direction' => 'rtl',
            'calendar' => 'persian'
        ),
        'ar' => array(
            'direction' => 'rtl',
            'calendar' => 'islamic'
        ),
        'ckb' => array(
            'direction' => 'rtl',
            'calendar' => 'persian'
        ),
        'he_IL' => array(
            'direction' => 'rtl',
            'calendar' => 'hebrew'
        ),
        'ug_CN' => array(
            'direction' => 'rtl',
        ),
        'dv' => array(
            'direction' => 'rtl',
        ),
        'ha' => array(
            'direction' => 'rtl',
        ),
        'ps' => array(
            'direction' => 'rtl',
        ),
        'uz_UZ' => array(
            'direction' => 'rtl',
        ),
        'yi' => array(
            'direction' => 'rtl',
        )
    );

    public function __construct(StorageInterface $storage, $defaultLocale)
    {
        $this->storage = $storage;
        $this->defaultLocale = $defaultLocale;
    }

    /**
     * {@inheritdoc}
     */
    public function getDefaultLocale()
    {
        return $this->defaultLocale;
    }

    /**
     * {@inheritdoc}
     */
    public function getLocale()
    {
        return $this->storage->getData(self::STORAGE_KEY, $this->defaultLocale);
    }

    /**
     * {@inheritdoc}
     */
    public function setLocale($locale)
    {
        return $this->storage->setData(self::STORAGE_KEY, $locale);
    }

    /**
     * @inheritdoc
     */
    public function getCalendar()
    {
        $code = $this->getLocale();
        return @self::$localeMetaData[$code]['calendar'] ?: 'gregorian';
    }

    /**
     * Get the currently active language direction.
     *
     * @return string
     */
    public function getDirection()
    {
        $code = $this->getLocale();
        return @self::$localeMetaData[$code]['direction'] ?: 'ltr';
    }
}
