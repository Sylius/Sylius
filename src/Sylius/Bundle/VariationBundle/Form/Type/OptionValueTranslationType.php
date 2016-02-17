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
 * @author Vincenzo Provenza <vincenzo.provenza89@gmail.com>
 */
class OptionValueTranslationType extends AbstractResourceType
{
    /**
     * @var string
     */
    protected $variableName;

    /**
     * @param string $dataClass
     * @param array $validationGroups
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
            ->add('value', 'text', [
                'label' => 'sylius.form.option_value.value',
            ])
        ;
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return sprintf('sylius_%s_option_value_translation', $this->variableName);
    }
}
