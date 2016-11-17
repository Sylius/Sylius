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
use Sylius\Bundle\ResourceBundle\Form\Type\ResourceChoiceType;
use Sylius\Component\Addressing\Model\ZoneInterface;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
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
        $builder
            ->addEventSubscriber(new AddCodeFormSubscriber())
            ->add('name', TextType::class, [
                'label' => 'sylius.form.zone.name',
            ])
            ->add('type', ZoneTypeChoiceType::class, [
                'disabled' => true,
            ])
            ->add('members', CollectionType::class, [
                'entry_type' => ZoneMemberType::class,
                'entry_options' => [
                    'zone_type' => $builder->getData()->getType(),
                ],
                'button_add_label' => 'sylius.form.zone.add_member',
                'allow_add' => true,
                'allow_delete' => true,
                'by_reference' => false,
                'delete_empty' => true,
            ])
        ;

        if (!empty($this->scopeChoices)) {
            $builder
                ->add('scope', ChoiceType::class, [
                    'label' => 'sylius.form.zone.scope',
                    'placeholder' => 'sylius.form.zone.select_scope',
                    'required' => false,
                    'choices' => $this->scopeChoices,
                ])
            ;
        }
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'sylius_zone';
    }
}
