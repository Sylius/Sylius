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

use Sylius\Bundle\ResourceBundle\Form\DataTransformer\CollectionToStringTransformer;
use Sylius\Bundle\ResourceBundle\Form\DataTransformer\RecursiveTransformer;
use Sylius\Bundle\ResourceBundle\Form\DataTransformer\ResourceToIdentifierTransformer;
use Sylius\Component\Registry\ServiceRegistryInterface;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\Options;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * @author Arkadiusz Krakowiak <arkadiusz.krakowiak@lakion.com>
 */
class ResourceAutocompleteChoiceType extends AbstractType
{
    /**
     * @var ServiceRegistryInterface
     */
    protected $resourceRepositoryRegistry;

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
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        if (!$options['multiple']) {
            $builder->addModelTransformer(
                new ResourceToIdentifierTransformer(
                    $options['repository'],
                    $options['choice_value']
                )
            );
        }

        if ($options['multiple']) {
            $builder
                ->addModelTransformer(
                    new RecursiveTransformer(
                        new ResourceToIdentifierTransformer(
                            $options['repository'],
                            $options['choice_value']
                        )
                    )
                )
                ->addViewTransformer(new CollectionToStringTransformer(','))
            ;
        }
    }

    /**
     * {@inheritdoc}
     */
    public function buildView(FormView $view, FormInterface $form, array $options)
    {
        $view->vars['multiple'] = $options['multiple'];
        $view->vars['choice_name'] = $options['choice_name'];
        $view->vars['choice_value'] = $options['choice_value'];
        $view->vars['placeholder'] = $options['placeholder'];
        $view->vars['remote_route_parameters'] = $options['remote_route_parameters'];
        $view->vars['remote_route'] = $options['remote_route'];
        $view->vars['remote_criteria_name'] = $options['remote_criteria_name'];
        $view->vars['remote_criteria_type'] = $options['remote_criteria_type'];
        if (isset($options['remote_url'])) {
            $view->vars['remote_url'] = $options['remote_url'];
        }
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver
            ->setRequired([
                'resource',
                'choice_name',
                'choice_value',
            ])
            ->setDefaults([
                'multiple' => false,
                'placeholder' => '',
                'repository' => function (Options $options) {
                    return $this->resourceRepositoryRegistry->get($options['resource']);
                },
                'remote_route' => '',
                'remote_route_parameters' => [],
                'remote_criteria_name' => 'name',
                'remote_criteria_type' => 'contains',
            ])
            ->setDefined('remote_url')
            ->setAllowedTypes('resource', ['string'])
            ->setAllowedTypes('multiple', ['bool'])
            ->setAllowedTypes('choice_name', ['string'])
            ->setAllowedTypes('choice_value', ['string'])
            ->setAllowedTypes('placeholder', ['string'])
            ->setAllowedTypes('remote_route', ['string'])
            ->setAllowedTypes('remote_route_parameters', ['array'])
            ->setAllowedTypes('remote_url', ['string'])
            ->setAllowedTypes('remote_criteria_name', ['string'])
            ->setAllowedTypes('remote_criteria_type', ['string'])
        ;
    }

    /**
     * {@inheritdoc}
     */
    public function getParent()
    {
        return HiddenType::class;
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'sylius_resource_autocomplete_choice';
    }
}
