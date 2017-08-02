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

namespace Sylius\Bundle\ResourceBundle\Form\Type;

use Sylius\Component\Registry\ServiceRegistryInterface;
use Sylius\Component\Resource\Metadata\RegistryInterface;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;

/**
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 */
final class DefaultResourceType extends AbstractType
{
    /**
     * @var RegistryInterface
     */
    private $metadataRegistry;

    /**
     * @var ServiceRegistryInterface
     */
    private $formBuilderRegistry;

    /**
     * @param RegistryInterface $metadataRegistry
     * @param ServiceRegistryInterface $formBuilderRegistry
     */
    public function __construct(RegistryInterface $metadataRegistry, ServiceRegistryInterface $formBuilderRegistry)
    {
        $this->metadataRegistry = $metadataRegistry;
        $this->formBuilderRegistry = $formBuilderRegistry;
    }

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $metadata = $this->metadataRegistry->getByClass($options['data_class']);
        $formBuilder = $this->formBuilderRegistry->get($metadata->getDriver());

        $formBuilder->build($metadata, $builder, $options);
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'sylius_resource';
    }
}
