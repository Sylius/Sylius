<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\ResourceBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

/**
 * @author aRnOD (Arnaud Langlade) <arnod.dev@gmail.com>
 */
class BaseFormType extends AbstractType
{
    /**
     * @var string
     */
    protected $dataClass;

    /**
     * @var array
     */
    protected $validationGroups;

    /**
     * Constructor.
     *
     * @param string $dataClass
     * @param array $validationGroups
     */
    public function __construct($dataClass, array $validationGroups)
    {
        $this->dataClass = $dataClass;
        $this->validationGroups = $validationGroups;
    }

    /**
     * {@inheritdoc}
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver
            ->setDefaults(array(
                'data_class' => $this->dataClass,
                'validation_groups' => $this->validationGroups,
            ))
        ;
    }
}
