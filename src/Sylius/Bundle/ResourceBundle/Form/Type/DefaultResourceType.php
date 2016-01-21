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

use Sylius\Bundle\ResourceBundle\Form\Builder\DefaultFormBuilderInterface;
use Sylius\Component\Resource\Metadata\MetadataInterface;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;

/**
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 */
class DefaultResourceType extends AbstractType
{
    /**
     * @var MetadataInterface
     */
    private $metadata;

    /**
     * @var DefaultFormBuilderInterface
     */
    private $defaultFormBuilder;

    /**
     * @param MetadataInterface $metadata
     * @param DefaultFormBuilderInterface $defaultFormBuilder
     */
    public function __construct(MetadataInterface $metadata, DefaultFormBuilderInterface $defaultFormBuilder)
    {
        $this->metadata = $metadata;
        $this->defaultFormBuilder = $defaultFormBuilder;
    }

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $this->defaultFormBuilder->build($this->metadata, $builder, $options);
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return sprintf('%s_%s', $this->metadata->getApplicationName(), $this->metadata->getName());
    }
}
