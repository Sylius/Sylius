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

use Sylius\Bundle\ResourceBundle\Form\DataTransformer\ResourceAutocompleteToIdentifierTransformer;
use Sylius\Component\Resource\Metadata\MetadataInterface;
use Sylius\Component\Resource\Repository\RepositoryInterface;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * @author Anna Walasek <anna.walasek@lakion.com>
 */
class ResourceAutocompleteType extends AbstractType
{
    /**
     * @var MetadataInterface
     */
    protected $metadata;

    /**
     * @param MetadataInterface $metadata
     */
    public function __construct(MetadataInterface $metadata)
    {
        $this->metadata = $metadata;
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'select' => 'name',
            'data_class' => null,
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('select', 'choice')
            ->add('resource', sprintf('%s_%s_to_hidden_identifier', $this->metadata->getApplicationName(), $this->metadata->getName()));

        $builder->addModelTransformer(new ResourceAutocompleteToIdentifierTransformer($options));
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return sprintf('%s_%s_autocomplete', $this->metadata->getApplicationName(), $this->metadata->getName());
    }
}
