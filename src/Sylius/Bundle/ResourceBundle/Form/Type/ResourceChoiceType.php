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

use Sylius\Bundle\ResourceBundle\DependencyInjection\Driver\Exception\UnknownDriverException;
use Sylius\Bundle\ResourceBundle\SyliusResourceBundle;
use Sylius\Component\Resource\Metadata\MetadataInterface;
use Sylius\Component\Resource\Metadata\RegistryInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\OptionsResolver\Options;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * @author Aleksey Bannov <a.s.bannov@gmail.com>
 * @author Anna Walasek <anna.walasek@gmail.com>
 */
final class ResourceChoiceType extends AbstractType
{
    /**
     * @var RegistryInterface
     */
    private $registryMetadataRegistry;

    /**
     * @param RegistryInterface $registryMetadataRegistry
     */
    public function __construct(RegistryInterface $registryMetadataRegistry)
    {
        $this->registryMetadataRegistry = $registryMetadataRegistry;
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver
            ->setRequired('resource')
            ->setAllowedTypes('resource', 'string')
            ->setDefault('class', null)
            ->setNormalizer('class', function (Options $options) {
                return $this->registryMetadataRegistry->get($options['resource'])->getClass('model');
            })
        ;
    }

    /**
     * {@inheritdoc}
     */
    public function getParent()
    {
        return EntityType::class;
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'sylius_resource_choice';
    }
}
