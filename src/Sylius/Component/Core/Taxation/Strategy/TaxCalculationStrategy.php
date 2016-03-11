<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Component\Core\Taxation\Strategy;

use Sylius\Bundle\SettingsBundle\Model\Settings;
use Sylius\Component\Addressing\Model\ZoneInterface;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\Taxation\Applicator\OrderTaxesApplicatorInterface;

/**
 * @author Mark McKelvie <mark.mckelvie@reiss.com>
 */
class TaxCalculationStrategy implements TaxCalculationStrategyInterface
{
    /**
     * @var string
     */
    protected $type;

    /**
     * @var Settings
     */
    protected $settings;

    /**
     * @var OrderTaxesApplicatorInterface[]
     */
    protected $applicators;

    /**
     * @param string $type
     * @param Settings $settings
     * @param OrderTaxesApplicatorInterface[] $applicators
     */
    public function __construct($type, Settings $settings, array $applicators)
    {
        $this->assertApplicatorsHaveCorrectType($applicators);

        $this->type = $type;
        $this->settings = $settings;
        $this->applicators = $applicators;
    }

    /**
     * {@inheritdoc}
     */
    public function applyTaxes(OrderInterface $order, ZoneInterface $zone)
    {
        foreach ($this->applicators as $applicator) {
            $applicator->apply($order, $zone);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * {@inheritdoc}
     */
    public function supports(OrderInterface $order, ZoneInterface $zone)
    {
        return $this->settings->get('default_tax_calculation_strategy') === $this->type;
    }

    /**
     * @param OrderTaxesApplicatorInterface[] $applicators
     */
    private function assertApplicatorsHaveCorrectType(array $applicators)
    {
        foreach ($applicators as $applicator) {
            if ($applicator instanceof OrderTaxesApplicatorInterface) {
                continue;
            }

            throw new \InvalidArgumentException(sprintf(
                'Order taxes applicator should have type "%s"',
                OrderTaxesApplicatorInterface::class
            ));
        }
    }
}
