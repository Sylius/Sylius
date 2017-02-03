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

use Sylius\Component\Registry\ServiceRegistryInterface;
use Sylius\Component\Resource\Repository\RepositoryInterface;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\OptionsResolver\Options;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * @author Aleksey Bannov <a.s.bannov@gmail.com>
 * @author Anna Walasek <anna.walasek@gmail.com>
 */
final class ResourceChoiceType extends AbstractType
{
    /**
     * @var ServiceRegistryInterface
     */
    private $resourceRepositoryRegistry;

    /**
     * @param ServiceRegistryInterface $resourceRepositoryRegistry
     */
    public function __construct(ServiceRegistryInterface $resourceRepositoryRegistry)
    {
        $this->resourceRepositoryRegistry = $resourceRepositoryRegistry;
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver
            ->setDefaults([
                'resource' => null,
                'choices' => function (Options $options) {
                    return $options['function']($this->resourceRepositoryRegistry->get($options['resource']), $options);
                },
                'function' => function (RepositoryInterface $repository, Options $options) {
                    return $repository->findAll();
                }
            ])
            ->setRequired(['resource'])
        ;
    }

    /**
     * {@inheritdoc}
     */
    public function getParent()
    {
        return ChoiceType::class;
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'sylius_resource_choice';
    }
}
