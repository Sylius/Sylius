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
 * @author Arnaud Langlade <arn0d.dev@gmail.com>
 */
abstract class AbstractResourceType extends AbstractType
{
    /**
     * @var string
     */
    protected $dataClass = null;

    /**
     * @var string[]
     */
    protected $validationGroups = array();

    /**
     * @var string
     */
    protected $translationDomain = null;

    /**
     * @param string   $dataClass        FQCN
     * @param string[] $validationGroups
     * @param string   $translationDomain
     */
    public function __construct($dataClass, array $validationGroups = array(), $translationDomain = 'messages')
    {
        $this->dataClass = $dataClass;
        $this->validationGroups = $validationGroups;
        $this->translationDomain = $translationDomain;
    }

    /**
     * {@inheritdoc}
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => $this->dataClass,
            'validation_groups' => $this->validationGroups,
            'translation_domain' => $this->translationDomain,
        ));
    }
}
