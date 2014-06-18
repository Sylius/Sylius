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

use Sylius\Bundle\VariationBundle\Form\ChoiceList\VariantChoiceList;
use Symfony\Bridge\Doctrine\Form\DataTransformer\CollectionToArrayTransformer;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\Options;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

/**
 * Variant choice form type.
 *
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 */
class VariantChoiceType extends AbstractType
{
    protected $variableName;

    public function __construct($variableName)
    {
      $this->variableName = $variableName;
    }

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        if ($options['multiple']) {
            $builder->addViewTransformer(new CollectionToArrayTransformer());
        }
    }

    /**
     * {@inheritdoc}
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $choiceList = function (Options $options) {
            return new VariantChoiceList($options['variable']);
        };

        $resolver
            ->setDefaults(array(
                'multiple'    => false,
                'expanded'    => true,
                'choice_list' => $choiceList
            ))
            ->setRequired(array(
                'variable'
            ))
            ->setAllowedTypes(array(
                'variable' => array('Sylius\Component\Variation\Model\VariableInterface')
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
        return sprintf('sylius_%s_variant_choice', $this->variableName);
    }
}
