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
use Symfony\Component\Form\FormBuilderInterface;

/**
 * Zone form type.
 *
 * @author Saša Stamenković <umpirsky@gmail.com>
 * @author Gonzalo Vilaseca <gvilaseca@reiss.co.uk>
 */
class ZoneType extends AbstractResourceType
{
    /**
     * Scopes.
     *
     * @var array
     */
    protected $scopeChoices;

    /**
     * Constructor.
     *
     * @param string   $dataClass
     * @param string[] $validationGroups
     * @param string[] $scopeChoices
     */
    public function __construct($dataClass, array $validationGroups, array $scopeChoices = array())
    {
        parent::__construct($dataClass, $validationGroups);

        $this->scopeChoices = $scopeChoices;
    }

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name', 'text', array(
                'label' => 'sylius.form.zone.name',
            ))
            ->add('type', 'sylius_zone_type_choice')
            ->add('members', 'sylius_zone_member_collection', array(
                'label' => 'sylius.form.zone.members',
            ))
        ;

        if (!empty($this->scopeChoices)) {
            $builder
                ->add('scope', 'choice', array(
                    'label'       => 'sylius.form.zone.scope',
                    'empty_value' => 'sylius.form.zone.select_scope',
                    'required'    => false,
                    'choices'     => $this->scopeChoices,
                ))
            ;
        }
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'sylius_zone';
    }
}
