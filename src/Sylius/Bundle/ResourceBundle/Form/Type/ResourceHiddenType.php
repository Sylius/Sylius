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

use Sylius\Bundle\ResourceBundle\Form\DataTransformer\ObjectToIdentifierTransformer;
use Sylius\Bundle\ResourceBundle\Form\DataTransformer\ResourceToIdentifierTransformer;
use Sylius\Component\Resource\Repository\ResourceRepositoryInterface;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 */
class ResourceHiddenType extends AbstractType
{
    /**
     * @var ResourceRepositoryInterface
     */
    protected $repository;

    /**
     * @var string
     */
    protected $name;

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
        $transformer = new ResourceToIdentifierTransformer($this->repository);

        $builder
            ->addViewTransformer($transformer)
            ->addEventListener(FormEvents::PRE_SUBMIT, function (FormEvent $event) use ($transformer) {
                $event->setData($transformer->reverseTransform($event->getData()));
            })
            ->addEventListener(FormEvents::SUBMIT, function (FormEvent $event) use ($transformer) {
                $event->setData($transformer->reverseTransform($event->getData()));
            })
        ;
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'identifier' => 'id',
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getParent()
    {
        return 'hidden';
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return $this->name;
    }
}
