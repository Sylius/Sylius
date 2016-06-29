<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\PromotionBundle\Form\Type\Core;

use Sylius\ResourceBundle\Form\Type\AbstractResourceType;
use Sylius\Registry\ServiceRegistryInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * @author Arnaud Langlade <arn0d.dev@gmail.com>
 */
abstract class AbstractConfigurationType extends AbstractResourceType
{
    /**
     * @var ServiceRegistryInterface
     */
    protected $registry;

    public function __construct($dataClass, ServiceRegistryInterface $registry)
    {
        parent::__construct($dataClass, ['Default']);

        $this->registry = $registry;
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        parent::configureOptions($resolver);

        $resolver->setDefined([
            'configuration_type',
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'sylius_promotion_action';
    }
}
