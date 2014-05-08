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
use Symfony\Component\Form\Extension\Core\ChoiceList\ObjectChoiceList;
use Symfony\Component\OptionsResolver\Options;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\PropertyAccess\PropertyAccess;

/**
 * Option value choice form type.
 *
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 */
class OptionValueChoiceType extends AbstractType
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
     * @param string $variableName
     */
    public function __construct($variableName)
    {
        $this->variableName = $variableName;
    }

    /**
     * {@inheritdoc}
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $choiceList = function (Options $options) {
            return new ObjectChoiceList($options['option']->getValues(), 'value', array(), null, null, PropertyAccess::createPropertyAccessor());
        };

        $resolver
            ->setDefaults(array(
                'choice_list' => $choiceList
            ))
            ->setRequired(array(
                'option'
            ))
            ->addAllowedTypes(array(
                'option' => 'Sylius\Component\Variation\Model\OptionInterface'
            ))
        ;
    }

    /**
     * {@inheritdoc}
     */
    public function getParent()
    {
        return 'choice';
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return sprintf('sylius_%s_option_value_choice', $this->variableName);
    }
}
