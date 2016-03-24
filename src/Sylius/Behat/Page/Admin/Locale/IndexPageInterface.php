<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Behat\Page\Admin\Locale;

use Sylius\Behat\Page\Admin\Crud\IndexPageInterface as BaseIndexPageInterface;
use Sylius\Component\Locale\Model\LocaleInterface;

/**
 * @author Arkadiusz Krakowiak <arkadiusz.krakowiak@lakion.com>
 */
interface IndexPageInterface extends BaseIndexPageInterface
{
    /**
     * @param LocaleInterface $locale
     *
     * @return bool
     */
    public function isLocaleDisabled(LocaleInterface $locale);

    /**
     * @param LocaleInterface $locale
     *
     * @return bool
     */
    public function isLocaleEnabled(LocaleInterface $locale);
}
