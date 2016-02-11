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

use Sylius\Component\Locale\Calendars;
use Sylius\Component\Storage\StorageInterface;

/**
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 * @author Joseph Bielawski <stloyd@gmail.com>
 * @author Aram Alipoor <aram.alipoor@gmail.com>
 */
class LocaleContext implements LocaleContextInterface
{
    // Key used to store the locale in storage.
    const STORAGE_KEY = '_sylius_locale';

    /**
     * @var string
     */
    protected $defaultLocale;

    /**
     * @var StorageInterface
     */
    protected $storage;

    /**
     * Meta data for special languages,
     * by default we assume that all languages are LTR, and calendar is gregorian,
     * except mentioned otherwise below.
     *
     * TODO Should be completed and extracted to an external library!
     *
     * @var array
     */
    protected static $localeMetaData = [
        'fa' => [
            'direction' => 'rtl',
            'calendar' => Calendars::PERSIAN,
        ],
        'fa_IR' => [
            'direction' => 'rtl',
            'calendar' => Calendars::PERSIAN,
        ],
        'fa_AF' => [
            'direction' => 'rtl',
            'calendar' => Calendars::PERSIAN,
        ],
        'ar' => [
            'direction' => 'rtl',
            'calendar' => Calendars::ISLAMIC,
        ],
        'ckb' => [
            'direction' => 'rtl',
            'calendar' => Calendars::PERSIAN,
        ],
        'he_IL' => [
            'direction' => 'rtl',
            'calendar' => Calendars::HEBREW,
        ],
        'ug_CN' => [
            'direction' => 'rtl',
        ],
        'dv' => [
            'direction' => 'rtl',
        ],
        'ha' => [
            'direction' => 'rtl',
        ],
        'ps' => [
            'direction' => 'rtl',
        ],
        'uz_UZ' => [
            'direction' => 'rtl',
        ],
        'yi' => [
            'direction' => 'rtl',
        ],
    ];

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
    public function getCurrentLocale()
    {
        return $this->storage->getData(self::STORAGE_KEY, $this->defaultLocale);
    }

    /**
     * {@inheritdoc}
     */
    public function setCurrentLocale($locale)
    {
        return $this->storage->setData(self::STORAGE_KEY, $locale);
    }

    /**
     * {@inheritdoc}
     */
    public function getCurrentCalendar()
    {
        $code = $this->getCurrentLocale();

        if (isset(self::$localeMetaData[$code]) && isset(self::$localeMetaData[$code]['calendar'])) {
            return self::$localeMetaData[$code]['calendar'];
        }

        return Calendars::GREGORIAN;
    }

    /**
     * {@inheritdoc}
     */
    public function getCurrentDirection()
    {
        $code = $this->getCurrentLocale();

        if (isset(self::$localeMetaData[$code]) && isset(self::$localeMetaData[$code]['calendar'])) {
            return self::$localeMetaData[$code]['direction'];
        }

        return 'ltr';
    }
}
