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
use Symfony\Component\Form\FormBuilderInterface;

/**
 * Taxation settings schema.
 *
 * @author Paweł Jędrzejewski <pjedrzejewski@diweb.pl>
 */
class TaxationSettingsSchema implements SchemaInterface
{
    /**
     * Zone repository.
     *
     * @var ObjectRepository
     */
    private $zoneRepository;

    /**
     * Constructor.
     *
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
            ->setOptional(array(
                'default_tax_zone'
            ))
            ->setAllowedTypes(array(
                'default_tax_zone' => array('null', 'Sylius\Bundle\AddressingBundle\Model\ZoneInterface'),
            ))
            ->setTransformer('default_tax_zone', new ObjectToIdentifierTransformer($this->zoneRepository))
        ;
    }

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder)
    {
        $builder
            ->add('default_tax_zone', 'sylius_zone_choice', array(
                'required' => false,
                'label'    => 'sylius.form.settings.taxation.default_tax_zone',
            ))
        ;
    }
}
