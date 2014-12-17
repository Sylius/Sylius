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

use Sylius\Bundle\ResourceBundle\Form\Type\AbstractResourceType;
use Symfony\Component\Form\FormBuilderInterface;

/**
 * Option form type.
 *
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 * @author Gonzalo Vilaseca <gvilaseca@reiss.co.uk>
 */
class OptionType extends AbstractResourceType
{
    /**
     * Variable object name.
     *
     * @var string
     */
    protected $variableName;

    /**
     * Constructor.
     *
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
            ->add('name', 'text', array(
                'label' => 'sylius.form.option.name'
            ))
            ->add('translations', 'a2lix_translationsForms', array(
                // TODO Form as a service?
                'form_type' => new OptionTranslationType($this->dataClass.'Translation', $this->validationGroups),
                'label'    => 'sylius.form.option.presentation'
            ))
            ->add('values', 'collection', array(
                'type'         => sprintf('sylius_%s_option_value', $this->variableName),
                'allow_add'    => true,
                'allow_delete' => true,
                'by_reference' => false,
                'label'        => 'sylius.form.option.values'
            ))
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
