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

use Sylius\Bundle\ResourceBundle\Form\Type\AbstractResourceType;
use Sylius\Component\Addressing\Model\ZoneInterface;
use Sylius\Component\Addressing\Model\ZoneMember;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * @author Jan Góralski <jan.goralski@lakion.com>
 */
class ZoneMemberType extends AbstractResourceType
{
    /**
     * @var array
     */
    protected static $zoneTypeToTypeClass = [
        ZoneInterface::TYPE_COUNTRY => CountryCodeChoiceType::class,
        ZoneInterface::TYPE_PROVINCE => ProvinceCodeChoiceType::class,
        ZoneInterface::TYPE_ZONE => ZoneCodeChoiceType::class,
    ];

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $zoneType = $options['zone_type'];

        $builder
            ->add('code', static::$zoneTypeToTypeClass[$zoneType], [
                'label' => 'sylius.form.zone.types.'.$zoneType,
                'required' => true,
            ])
        ;
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver
            ->setRequired('zone_type')
            ->setAllowedValues('zone_type', array_keys(static::$zoneTypeToTypeClass))
            ->setDefaults([
                'data_class' => ZoneMember::class,
                'placeholder' => 'sylius.form.zone_member.select',
            ])
        ;
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'sylius_zone_member';
    }
}
