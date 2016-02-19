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

use Sylius\Component\Variation\Model\OptionInterface;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Exception\InvalidConfigurationException;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * This is special collection type, inspired by original 'collection' type
 * implementation, designed to handle option values assigned to object variant.
 * Array of OptionInterface objects should be passed as 'options' option to build proper
 * set of choice types with option values list.
 *
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 */
class OptionValueCollectionType extends AbstractType
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
        if (!isset($options['options']) ||
            !is_array($options['options']) &&
            !($options['options'] instanceof \Traversable && $options['options'] instanceof \ArrayAccess)
        ) {
            throw new InvalidConfigurationException(
                'array or (\Traversable and \ArrayAccess) of "Sylius\Component\Variation\Model\OptionInterface" must be passed to collection'
            );
        }

        foreach ($options['options'] as $i => $option) {
            if (!$option instanceof OptionInterface) {
                throw new InvalidConfigurationException('Each object passed as option list must implement "Sylius\Component\Variation\Model\OptionInterface"');
            }

            $builder->add((string) $option->getId(), sprintf('sylius_%s_option_value_choice', $this->variableName), [
                'label' => $option->getName() ?: $option->getCode(),
                'option' => $option,
                'property_path' => '['.$i.']',
            ]);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver
            ->setDefaults([
                'options' => null,
            ])
        ;
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return sprintf('sylius_%s_option_value_collection', $this->variableName);
    }
}
