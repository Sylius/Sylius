<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\CoreBundle\Controller;

use Sylius\Bundle\CoreBundle\Locale\ChannelAwareLocaleProvider;
use Sylius\Bundle\LocaleBundle\Controller\LocaleController as BaseLocaleController;

/**
 * Locale controller which is Channel aware.
 *
 * @author Aram Alipoor <aram.alipoor@gmail.com>
 */
class LocaleController extends BaseLocaleController
{
    /**
     * @return ChannelAwareLocaleProvider
     */
    protected function getLocaleProvider()
    {
        return $this->get('sylius.channel_aware_locale_provider');
    }
}
