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
    private $registry;

    /**
     * @var DefaultFormBuilderInterface
     */
    private $defaultFormBuilder;

    /**
     * @param RegistryInterface $registry
     * @param DefaultFormBuilderInterface $defaultFormBuilder
     */
    public function __construct(RegistryInterface $registry, DefaultFormBuilderInterface $defaultFormBuilder)
    {
        $this->registry = $registry;
        $this->defaultFormBuilder = $defaultFormBuilder;
    }

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $metadata = $this->registry->getByClass($options['data_class']);

        $this->defaultFormBuilder->build($metadata, $builder, $options);
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'sylius_resource';
    }
}
