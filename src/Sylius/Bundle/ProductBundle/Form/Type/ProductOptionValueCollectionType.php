<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Sylius\Bundle\ProductBundle\Form\Type;

use Sylius\Component\Product\Model\ProductOptionInterface;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Exception\InvalidConfigurationException;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Webmozart\Assert\Assert;

/**
 * This is special collection type, inspired by original 'collection' type
 * implementation, designed to handle option values assigned to object variant.
 * Array of OptionInterface objects should be passed as 'options' option to build proper
 * set of choice types with option values list.
 */
final class ProductOptionValueCollectionType extends AbstractType
{
    /**
     * {@inheritdoc}
     *
     * @throws \InvalidArgumentException
     * @throws InvalidConfigurationException
     */
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $this->assertOptionsAreValid($options);

        foreach ($options['options'] as $i => $option) {
            if (!$option instanceof ProductOptionInterface) {
                throw new InvalidConfigurationException(
                    sprintf('Each object passed as option list must implement "%s"', ProductOptionInterface::class)
                );
            }

            $builder->add((string) $option->getCode(), ProductOptionValueChoiceType::class, [
                'label' => $option->getName() ?: $option->getCode(),
                'option' => $option,
                'property_path' => '[' . $i . ']',
                'block_name' => 'entry',
            ]);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver): void
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
    public function getBlockPrefix(): string
    {
        return 'sylius_product_option_value_collection';
    }

    /**
     * @param mixed $options
     *
     * @throws \InvalidArgumentException
     */
    private function assertOptionsAreValid($options): void
    {
        Assert::true(
            isset($options['options']) && is_iterable($options['options']),
            'array or (\Traversable and \ArrayAccess) of "Sylius\Component\Variation\Model\OptionInterface" must be passed to collection'
        );
    }
}
