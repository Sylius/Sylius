<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\CoreBundle\Twig;

use Sylius\Bundle\MoneyBundle\Twig\SyliusMoneyExtension as BaseSyliusMoneyExtension;
use Sylius\Bundle\SettingsBundle\Manager\SettingsManagerInterface;

class SyliusMoneyExtension extends BaseSyliusMoneyExtension
{
    public function __construct(SettingsManagerInterface $settingsManager, $locale = null)
    {
        parent::__construct(
            $settingsManager->loadSettings('general')->get('currency'),
            $locale
        );
    }
}
