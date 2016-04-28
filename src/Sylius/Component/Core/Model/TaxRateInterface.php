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
use Sylius\Component\Taxation\Model\TaxRateInterface as BaseTaxRateInterface;

/**
 * Tax rate interface.
 *
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 * @author Robin Jansen <robinjansen51@gmail.com>
 */
interface TaxRateInterface extends BaseTaxRateInterface
{
    /**
     * Get zone.
     *
     * @return ZoneInterface
     */
    public function getZone();

    /**
     * Set zone.
     *
     * @param ZoneInterface $zone
     */
    public function setZone(ZoneInterface $zone);

    /**
     * @return bool
     */
    public function isAppliedToIndividuals();

    /**
     * @return bool
     */
    public function getAppliedToIndividuals();

    /**
     * @param bool $appliedToIndividuals
     */
    public function setAppliedToIndividuals($appliedToIndividuals);

    /**
     * @return bool
     */
    public function isAppliedToEntrepreneursAndResellers();

    /**
     * @return bool
     */
    public function getAppliedToEntrepreneursAndResellers();

    /**
     * @param bool $appliedToResellers
     */
    public function setAppliedToEntrepreneursAndResellers($appliedToResellers);
}
