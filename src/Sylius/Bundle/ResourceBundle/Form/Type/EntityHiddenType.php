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

use Doctrine\Common\Persistence\ManagerRegistry;
use Sylius\Bundle\ResourceBundle\Form\DataTransformer\ObjectToIdentifierTransformer;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Exception\LogicException;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;

class EntityHiddenType extends AbstractType
{
    /**
     * Manager registry.
     *
     * @var ManagerRegistry
     */
    protected $manager;

    public function __construct(ManagerRegistry $manager)
    {
        $this->manager = $manager;
    }

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        if (!$options['data_class']) {
            throw new LogicException('Option "data_class" must be set.');
        }

        $transformer = new ObjectToIdentifierTransformer($this->manager->getRepository($options['data_class']), $options['identifier']);

        $builder
            ->addViewTransformer($transformer)
            ->setAttribute('data_class', $options['data_class'])
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
        return 'entity_hidden';
    }
}
