<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\SettingsBundle\Model;

use Doctrine\Common\Collections\ArrayCollection;

/**
 * @author Steffen Brem <steffenbrem@gmail.com>
 */
class ParameterCollection extends ArrayCollection
{
    /**
     * @var SettingsInterface
     */
    protected $settings;

    public function __construct(SettingsInterface $settings, array $elements)
    {
        parent::__construct($elements);

        $this->settings = $settings;
    }

    /**
     * @return SettingsInterface
     */
    public function getSettings()
    {
        return $this->settings;
    }
}
