<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\CoreBundle\Taxation\Strategy;

use Sylius\SettingsBundle\Model\SettingsInterface;
use Sylius\Addressing\Model\ZoneInterface;
use Sylius\Core\Model\OrderInterface;
use Sylius\Core\Taxation\Applicator\OrderTaxesApplicatorInterface;
use Sylius\Core\Taxation\Strategy\AbstractTaxCalculationStrategy;

/**
 * @author Mark McKelvie <mark.mckelvie@reiss.com>
 */
class TaxCalculationStrategy extends AbstractTaxCalculationStrategy
{
    /**
     * @var SettingsInterface
     */
    protected $settings;

    /**
     * @param string $type
     * @param OrderTaxesApplicatorInterface[] $applicators
     * @param SettingsInterface $settings
     */
    public function __construct($type, array $applicators, SettingsInterface $settings)
    {
        parent::__construct($type, $applicators);

        $this->settings = $settings;
    }

    /**
     * {@inheritdoc}
     */
    public function supports(OrderInterface $order, ZoneInterface $zone)
    {
        return $this->settings->get('default_tax_calculation_strategy') === $this->type;
    }
}
