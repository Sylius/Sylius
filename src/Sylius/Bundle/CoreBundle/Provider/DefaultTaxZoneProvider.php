<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\CoreBundle\Provider;

use Sylius\Bundle\SettingsBundle\Model\ParameterCollection;
use Sylius\Component\Core\Provider\ZoneProviderInterface;

/**
 * @author Mateusz Zalewski <mateusz.zalewski@lakion.com>
 */
class DefaultTaxZoneProvider implements ZoneProviderInterface
{
    /**
     * @var ParameterCollection
     */
    private $settings;

    /**
     * @param ParameterCollection $settings
     */
    public function __construct(ParameterCollection $settings)
    {
        $this->settings = $settings;
    }

    /**
     * {@inheritdoc}
     */
    public function getZone()
    {
        if ($zone = $this->settings->get('default_tax_zone')) {
            return $zone;
        }

        return null;
    }
}
