<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\CoreBundle\Form\EventSubscriber;

use Sylius\Bundle\ShippingBundle\Form\EventSubscriber\BuildShippingMethodFormSubscriber as BaseBuildShippingMethodFormSubscriber;
use Sylius\Component\Shipping\Calculator\CalculatorInterface;
use Symfony\Component\Form\FormInterface;

/**
 * @author Grzegorz Sadowski <grzegorz.sadowski@lakion.com>
 */
class BuildShippingMethodFormSubscriber extends BaseBuildShippingMethodFormSubscriber
{
    /**
     * @param FormInterface $form
     * @param string $calculatorName
     * @param array $data
     */
    protected function addConfigurationFields(FormInterface $form, $calculatorName, array $data = [])
    {
        /** @var CalculatorInterface $calculator */
        $calculator = $this->calculatorRegistry->get($calculatorName);

        $calculatorTypeName = sprintf('sylius_channel_based_shipping_calculator_%s', $calculator->getType());

        if (!$this->formRegistry->hasType($calculatorTypeName)) {
            return;
        }

        $configurationField = $this->factory->createNamed(
            'configuration',
            $calculatorTypeName,
            $data,
            ['auto_initialize' => false]
        );

        $form->add($configurationField);
    }
}
