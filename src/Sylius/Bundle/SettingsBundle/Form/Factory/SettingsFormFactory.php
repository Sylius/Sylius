<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\SettingsBundle\Form\Factory;

use Sylius\Bundle\SettingsBundle\Schema\SchemaRegistryInterface;
use Symfony\Component\Form\FormFactoryInterface;

/**
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 */
class SettingsFormFactory implements SettingsFormFactoryInterface
{
    /**
     * @var SchemaRegistryInterface
     */
    protected $schemaRegistry;

    /**
     * @var FormFactoryInterface
     */
    protected $formFactory;

    /**
     * @param SchemaRegistryInterface $schemaRegistry
     * @param FormFactoryInterface    $formFactory
     */
    public function __construct(SchemaRegistryInterface $schemaRegistry, FormFactoryInterface $formFactory)
    {
        $this->schemaRegistry = $schemaRegistry;
        $this->formFactory = $formFactory;
    }

    /**
     * {@inheritdoc}
     */
    public function create($namespace, $data = null, array $options = [])
    {
        $schema = $this->schemaRegistry->getSchema($namespace);
        $builder = $this->formFactory->createBuilder('form', $data, array_merge_recursive(
            ['data_class' => null], $options
        ));

        $schema->buildForm($builder);

        return $builder->getForm();
    }
}
