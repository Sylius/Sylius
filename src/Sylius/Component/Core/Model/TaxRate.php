<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Component\Core\Model;

use Sylius\Component\Addressing\Model\ZoneInterface;
use Sylius\Component\Taxation\Model\TaxRate as BaseTaxRate;

/**
 * Tax rate applicable to selected zone.
 *
 * @author Paweł Jędrzejewski <pjedrzejewski@diweb.pl>
 */
class TaxRate extends BaseTaxRate implements TaxRateInterface
{
    /**
     * Tax zone.
     *
     * @var ZoneInterface
     */
    protected $zone;

    /**
     * {@inheritdoc}
     */
    public function getZone()
    {
        return $this->zone;
    }

    /**
     * {@inheritdoc}
     */
    public function setZone(ZoneInterface $zone)
    {
        $this->zone = $zone;

        return $this;
    }
}
