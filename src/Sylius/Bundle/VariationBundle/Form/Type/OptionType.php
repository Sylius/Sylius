<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\VariationBundle\Form\Type;

use Sylius\Bundle\ResourceBundle\Form\EventSubscriber\AddCodeFormSubscriber;
use Sylius\Bundle\ResourceBundle\Form\Type\AbstractResourceType;
use Symfony\Component\Form\FormBuilderInterface;

/**
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 * @author Gonzalo Vilaseca <gvilaseca@reiss.co.uk>
 */
class OptionType extends AbstractResourceType
{
    /**
     * @var string
     */
    protected $variableName;

    /**
     * @param string $dataClass
     * @param array  $validationGroups
     * @param string $variableName
     */
    public function __construct($dataClass, array $validationGroups, $variableName)
    {
        parent::__construct($dataClass, $validationGroups);

        $this->variableName = $variableName;
    }

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('translations', 'sylius_translations', [
                'type' => sprintf('sylius_%s_option_translation', $this->variableName),
                'label' => 'sylius.form.option.name',
            ])
            ->add('values', 'collection', [
                'type' => sprintf('sylius_%s_option_value', $this->variableName),
                'allow_add' => true,
                'allow_delete' => true,
                'by_reference' => false,
                'label' => false,
                'button_add_label' => 'sylius.form.option_value.add_value',
            ])
            ->addEventSubscriber(new AddCodeFormSubscriber())
        ;
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return sprintf('sylius_%s_option', $this->variableName);
    }
}
