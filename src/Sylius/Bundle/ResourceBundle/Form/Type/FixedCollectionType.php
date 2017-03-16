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
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\Options;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * @author Kamil Kokot <kamil.kokot@lakion.com>
 */
final class FixedCollectionType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        foreach ($options['entries'] as $entry) {
            $entryType = $options['entry_type']($entry);
            $entryName = $options['entry_name']($entry);
            $entryOptions = $options['entry_options']($entry);

            $builder->add($entryName, $entryType, array_replace([
                'property_path' => '[' . $entryName . ']',
                'block_name' => 'entry',
            ], $entryOptions));
        }
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setRequired('entries');
        $resolver->setAllowedTypes('entries', ['array']);

        $resolver->setRequired('entry_type');
        $resolver->setAllowedTypes('entry_type', ['string', 'callable']);
        $resolver->setNormalizer('entry_type', $this->optionalCallableNormalizer());

        $resolver->setRequired('entry_name');
        $resolver->setAllowedTypes('entry_name', ['callable']);

        $resolver->setDefault('entry_options', function () { return []; });
        $resolver->setAllowedTypes('entry_options', ['array', 'callable']);
        $resolver->setNormalizer('entry_options', $this->optionalCallableNormalizer());
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'sylius_fixed_collection';
    }

    /**
     * @return callable
     */
    private function optionalCallableNormalizer()
    {
        return function (Options $options, $value) {
            if (is_callable($value)) {
                return $value;
            }

            return function () use ($value) { return $value; };
        };
    }
}
