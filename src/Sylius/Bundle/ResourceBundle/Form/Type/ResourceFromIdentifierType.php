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

use Sylius\Bundle\ResourceBundle\Form\DataTransformer\IdentifierToResourceTransformer;
use Sylius\Component\Resource\Metadata\MetadataInterface;
use Sylius\Component\Resource\Repository\RepositoryInterface;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * @author Anna Walasek <anna.walasek@lakion.com>
 */
class ResourceFromIdentifierType extends AbstractType
{
    /**
     * @var RepositoryInterface
     */
    protected $repository;

    /**
     * @var MetadataInterface
     */
    protected $metadata;

    /**
     * @param RepositoryInterface $repository
     * @param MetadataInterface $metadata
     */
    public function __construct(RepositoryInterface $repository, MetadataInterface $metadata)
    {
        $this->repository = $repository;
        $this->metadata = $metadata;
    }

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->addModelTransformer(
            new IdentifierToResourceTransformer($this->repository, $options['identifier'])
        );
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver
            ->setDefaults([
                'identifier' => 'id',
            ])
            ->setAllowedTypes('identifier', 'string')
        ;
    }

    /**
     * {@inheritdoc}
     */
    public function getParent()
    {
        return 'entity';
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return sprintf('%s_%s_from_identifier', $this->metadata->getApplicationName(), $this->metadata->getName());
    }
}
