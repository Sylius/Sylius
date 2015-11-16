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

use Sylius\Bundle\ResourceBundle\Form\DataTransformer\ResourceToIdentifierTransformer;
use Sylius\Component\Resource\Repository\ResourceRepositoryInterface;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Object to identifier type.
 *
 * @author Alexandre Bacco <alexandre.bacco@gmail.com>
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 */
class ResourceToIdentifierType extends AbstractType
{
    /**
     * Repository.
     *
     * @var ResourceRepositoryInterface
     */
    protected $repository;

    /**
     * Form name.
     *
     * @var string
     */
    protected $name;

    /**
     * @param ResourceRepositoryInterface $repository
     * @param string $name
     */
    public function __construct(ResourceRepositoryInterface $repository, $name)
    {
        $this->repository = $repository;
        $this->name = $name;
    }

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->addModelTransformer(
            new ResourceToIdentifierTransformer($this->repository)
        );
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver
            ->setDefaults(array(
                'identifier' => 'id',
            ))
            ->setAllowedTypes('identifier', 'string')
        ;
    }

    /**
     * {@inheritdoc}
     */
    public function getParent()
    {
        return 'text';
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return $this->name;
    }
}
