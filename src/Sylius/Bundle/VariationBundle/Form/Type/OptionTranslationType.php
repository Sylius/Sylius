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
 * @author Gonzalo Vilaseca <gvilaseca@reiss.co.uk>
 */
class OptionTranslationType extends AbstractResourceType
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
            ->add('name', 'text', [
                'label' => 'sylius.form.option.name',
            ])
        ;
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return sprintf('sylius_%s_option_translation', $this->variableName);
    }
}
