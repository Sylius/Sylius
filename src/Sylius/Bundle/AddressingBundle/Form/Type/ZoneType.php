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

use Sylius\Bundle\ResourceBundle\Form\EventSubscriber\AddCodeFormSubscriber;
use Sylius\Bundle\ResourceBundle\Form\Type\AbstractResourceType;
use Sylius\Component\Addressing\Model\ZoneInterface;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * @author Saša Stamenković <umpirsky@gmail.com>
 * @author Gonzalo Vilaseca <gvilaseca@reiss.co.uk>
 * @author Jan Góralski <jan.goralski@lakion.com>
 */
class ZoneType extends AbstractResourceType
{
    /**
     * @var array
     */
    protected $scopeChoices;

    /**
     * @param string   $dataClass
     * @param string[] $validationGroups
     * @param string[] $scopeChoices
     */
    public function __construct($dataClass, array $validationGroups, array $scopeChoices = [])
    {
        parent::__construct($dataClass, $validationGroups);

        $this->scopeChoices = $scopeChoices;
    }

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $zoneType = $builder->getData()->getType();

        $builder
            ->addEventSubscriber(new AddCodeFormSubscriber())
            ->add('name', 'text', [
                'label' => 'sylius.form.zone.name',
            ])
            ->add('type', 'sylius_zone_type_choice', [
                'disabled' => true,
            ])
            ->add('members', 'collection', [
                'type' => 'sylius_zone_member',
                'button_add_label' => 'sylius.form.zone.add_member',
                'allow_add' => true,
                'allow_delete' => true,
                'by_reference' => false,
                'delete_empty' => true,
                'options' => [
                    'zone_type' => $zoneType,
                ],
            ])
        ;

        if (!empty($this->scopeChoices)) {
            $builder
                ->add('scope', 'choice', [
                    'label' => 'sylius.form.zone.scope',
                    'empty_value' => 'sylius.form.zone.select_scope',
                    'required' => false,
                    'choices' => $this->scopeChoices,
                ])
            ;
        }
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        parent::configureOptions($resolver);

        $resolver->setDefault('zone_type', ZoneInterface::TYPE_COUNTRY);
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'sylius_zone';
    }
}
