<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\CoreBundle\Settings;

use Doctrine\Common\Persistence\ObjectRepository;
use Sylius\Bundle\SettingsBundle\Schema\SchemaInterface;
use Sylius\Bundle\SettingsBundle\Schema\SettingsBuilderInterface;
use Sylius\Bundle\SettingsBundle\Transformer\ObjectToIdentifierTransformer;
use Sylius\Component\Addressing\Model\ZoneInterface;
use Symfony\Component\Form\FormBuilderInterface;

/**
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 */
class TaxationSettingsSchema implements SchemaInterface
{
    /**
     * @var ObjectRepository
     */
    private $zoneRepository;

    /**
     * @param ObjectRepository $zoneRepository
     */
    public function __construct(ObjectRepository $zoneRepository)
    {
        $this->zoneRepository = $zoneRepository;
    }

    /**
     * {@inheritdoc}
     */
    public function buildSettings(SettingsBuilderInterface $builder)
    {
        $builder
            ->setDefaults([
                'default_tax_zone' => null,
            ])
            ->setAllowedTypes('default_tax_zone', ['null', ZoneInterface::class])
            ->setTransformer('default_tax_zone', new ObjectToIdentifierTransformer($this->zoneRepository))
            ->setDefault('default_tax_calculation_strategy', 'order_items_based')
            ->setAllowedTypes('default_tax_calculation_strategy', 'string')
        ;
    }

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder)
    {
        $builder
            ->add('default_tax_zone', 'sylius_zone_choice', [
                'required' => false,
                'label' => 'sylius.form.settings.taxation.default_tax_zone',
            ])
            ->add('default_tax_calculation_strategy', 'sylius_tax_calculation_strategy_choice', [
                'required' => true,
                'label' => 'sylius.form.settings.taxation.default_tax_calculation_strategy',
            ])
        ;
    }
}
