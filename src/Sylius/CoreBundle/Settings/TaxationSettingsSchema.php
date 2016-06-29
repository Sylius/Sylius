<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\CoreBundle\Settings;

use Sylius\SettingsBundle\Schema\SchemaInterface;
use Sylius\SettingsBundle\Schema\SettingsBuilderInterface;
use Sylius\SettingsBundle\Transformer\ResourceToIdentifierTransformer;
use Sylius\Addressing\Model\ZoneInterface;
use Sylius\Resource\Repository\RepositoryInterface;
use Symfony\Component\Form\FormBuilderInterface;

/**
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 */
class TaxationSettingsSchema implements SchemaInterface
{
    /**
     * @var RepositoryInterface
     */
    private $zoneRepository;

    /**
     * @param RepositoryInterface $zoneRepository
     */
    public function __construct(RepositoryInterface $zoneRepository)
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
            ->setDefault('default_tax_calculation_strategy', 'order_items_based')
            ->setAllowedTypes('default_tax_calculation_strategy', 'string')
            ->setTransformer('default_tax_zone', new ResourceToIdentifierTransformer($this->zoneRepository))
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
