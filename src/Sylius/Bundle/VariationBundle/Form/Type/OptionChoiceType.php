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

use Symfony\Component\Form\AbstractType;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

/**
 * Option choice form type.
 *
 * @author Paweł Jędrzejewski <pjedrzejewski@diweb.pl>
 */
abstract class OptionChoiceType extends AbstractType
{
    /**
     * Variable name.
     *
     * @var string
     */
    protected $variableName;

    /**
     * Option class name.
     *
     * @var string
     */
    protected $className;

    /**
     * Constructor.
     *
     * @param string $variableName
     * @param string $className
     */
    public function __construct($variableName, $className)
    {
        $this->variableName = $variableName;
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
        return sprintf('sylius_%s_option_choice', $this->variableName);
    }
}
