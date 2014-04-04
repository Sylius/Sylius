<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\AttributeBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

/**
 * Attribute choice form type.
 *
 * @author Paweł Jędrzejewski <pjedrzejewski@diweb.pl>
 */
abstract class AttributeChoiceType extends AbstractType
{
    /**
     * Name of the attributes subject.
     *
     * @var string
     */
    protected $subjectName;

    /**
     * Attribute class name.
     *
     * @var string
     */
    protected $className;

    /**
     * Constructor.
     *
     * @param string $subjectName
     * @param string $className
     */
    public function __construct($subjectName, $className)
    {
        $this->subjectName = $subjectName;
        $this->className = $className;
    }

    /**
     * {@inheritdoc}
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver
            ->setDefaults(array(
                'class' => $this->className
            ))
        ;
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return sprintf('sylius_%s_attribute_choice', $this->subjectName);
    }
}
