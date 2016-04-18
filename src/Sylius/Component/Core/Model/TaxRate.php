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
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 * @author Robin Jansen <robinjansen51@gmail.com>
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
     * @var string
     */
    protected $appliedToIndividuals = true;

    /**
     * Used in United States
     *
     * @var string
     */
    protected $appliedToResellers = true;

    /**
     * Used in European Union
     *
     * @var string
     */
    protected $appliedToEntrepreneurs = true;

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

    /**
     * {@inheritdoc}
     */
    public function isAppliedToIndividuals()
    {
        return $this->appliedToIndividuals;
    }

    /**
     * {@inheritdoc}
     */
    public function getAppliedToIndividuals()
    {
        return $this->appliedToIndividuals;
    }

    /**
     * {@inheritdoc}
     */
    public function setAppliedToIndividuals($appliedToIndividuals)
    {
        $this->appliedToIndividuals = $appliedToIndividuals;
    }

    /**
     * {@inheritdoc}
     */
    public function IsAppliedToResellers()
    {
        return $this->appliedToResellers;
    }

    /**
     * {@inheritdoc}
     */
    public function getAppliedToResellers()
    {
        return $this->appliedToResellers;
    }

    /**
     * {@inheritdoc}
     */
    public function setAppliedToResellers($appliedToResellers)
    {
        $this->appliedToResellers = $appliedToResellers;
    }

    /**
     * {@inheritdoc}
     */
    public function isAppliedToEntrepreneurs()
    {
        return $this->appliedToEntrepreneurs;
    }


    /**
     * {@inheritdoc}
     */
    public function getAppliedToEntrepreneurs()
    {
        return $this->appliedToEntrepreneurs;
    }

    /**
     * {@inheritdoc}
     */
    public function setAppliedToEntrepreneurs($appliedToEntrepreneurs)
    {
        $this->appliedToEntrepreneurs = $appliedToEntrepreneurs;
    }
}
