<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\PromotionBundle\Form\Type\Core;

use Sylius\Component\Registry\ServiceRegistryInterface;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * @author Arnaud Langlade <arn0d.dev@gmail.com>
 */
abstract class AbstractConfigurationCollectionType extends AbstractType
{
    /**
     * @var ServiceRegistryInterface
     */
    protected $registry;

    public function __construct(ServiceRegistryInterface $registry)
    {
        $this->registry = $registry;
    }

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $prototypes = [];

        foreach (array_keys($this->registry->all()) as $type) {
            $prototypeOptions = array_replace(
                ['configuration_type' => $type],
                $options['options']
            );
            $form = $builder->create($options['prototype_name'], $options['type'], $prototypeOptions);

            $prototypes[$type] = $form->getForm();
        }

        $builder->setAttribute('prototypes', $prototypes);
    }

    /**
     * {@inheritdoc}
     */
    public function buildView(FormView $view, FormInterface $form, array $options)
    {
        $view->vars['prototypes'] = [];

        foreach ($form->getConfig()->getAttribute('prototypes') as $type => $prototype) {
            /* @var FormInterface $prototype */
            $view->vars['prototypes'][$type] = $prototype->createView($view);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'type' => $this->getFormTypeOption(),
            'allow_add' => true,
            'allow_delete' => true,
            'by_reference' => false,
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function getParent()
    {
        return 'collection';
    }

    /**
     * @return string
     */
    abstract public function getFormTypeOption();
}
