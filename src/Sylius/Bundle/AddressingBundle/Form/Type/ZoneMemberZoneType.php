<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\AddressingBundle\Form\Type;

use Symfony\Component\Form\FormBuilderInterface;

/**
 * Zone member zone form type.
 *
 * @author Саша Стаменковић <umpirsky@gmail.com>
 */
class ZoneMemberZoneType extends ZoneMemberType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('zone', 'sylius_zone_choice', array(
                'label' => 'sylius.form.zone_member_zone.zone'
            ))
        ;

        parent::buildForm($builder, $options);
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'sylius_zone_member_zone';
    }
}
