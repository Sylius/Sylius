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

use Gedmo\Sluggable\Util\Urlizer;
use Sylius\Bundle\VariationBundle\Form\DataTransformer\VariantToCombinationTransformer;
use Sylius\Component\Variation\Model\VariableInterface;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 */
class VariantMatchType extends AbstractType
{
    /**
     * @var string
     */
    protected $variableName;

    /**
     * @param string $variableName
     */
    public function __construct($variableName)
    {
        $this->variableName = $variableName;
    }

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        foreach ($options['variable']->getOptions() as $i => $option) {
            $builder->add($option->getCode(), sprintf('sylius_%s_option_value_choice', $this->variableName), [
                'label' => $option->getName(),
                'option' => $option,
                'property_path' => '['.$i.']',
            ]);
        }

        $builder->addModelTransformer(new VariantToCombinationTransformer($options['variable']));
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver
            ->setRequired([
                'variable',
            ])
            ->setAllowedTypes('variable', VariableInterface::class)
        ;
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return sprintf('sylius_%s_variant_match', $this->variableName);
    }
}
