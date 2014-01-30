<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\CoreBundle\Form\Type;

use Sylius\Bundle\TaxationBundle\Form\Type\TaxRateType as BaseTaxRateType;
use Symfony\Component\Form\FormBuilderInterface;

/**
 * Tax rate form type, extended with zone selection field.
 *
 * @author Paweł Jędrzejewski <pjedrzejewski@diweb.pl>
 */
class TaxRateType extends BaseTaxRateType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        parent::buildForm($builder, $options);

        $builder->add('zone', 'sylius_zone_choice');
    }
}
