<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\ShippingBundle\Form\Type;

use Sylius\Component\Registry\ServiceRegistryInterface;
use Sylius\Component\Resource\Repository\RepositoryInterface;
use Sylius\Component\Shipping\Model\ShippingMethodInterface;
use Sylius\Component\Shipping\Model\ShippingSubjectInterface;
use Sylius\Component\Shipping\Resolver\ShippingMethodsResolverInterface;
use Symfony\Bridge\Doctrine\Form\DataTransformer\CollectionToArrayTransformer;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Exception\UnexpectedTypeException;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\Options;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 */
final class ShippingMethodChoiceType extends AbstractType
{
    /**
     * @var ShippingMethodsResolverInterface
     */
    private $shippingMethodsResolver;

    /**
     * @var ServiceRegistryInterface
     */
    private $calculators;

    /**
     * @var RepositoryInterface
     */
    private $repository;

    /**
     * @param ShippingMethodsResolverInterface $shippingMethodsResolver
     * @param ServiceRegistryInterface $calculators
     * @param RepositoryInterface $repository
     */
    public function __construct(
        ShippingMethodsResolverInterface $shippingMethodsResolver,
        ServiceRegistryInterface $calculators,
        RepositoryInterface $repository
    ) {
        $this->shippingMethodsResolver = $shippingMethodsResolver;
        $this->calculators = $calculators;
        $this->repository = $repository;
    }

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        if ($options['multiple']) {
            $builder->addModelTransformer(new CollectionToArrayTransformer());
        }
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver
            ->setDefaults([
                'choices' => function (Options $options) {
                    if (isset($options['subject']) && $this->shippingMethodsResolver->supports($options['subject'])) {
                        return $this->shippingMethodsResolver->getSupportedMethods($options['subject']);
                    }

                    return $this->repository->findAll();
                },
                'choice_value' => 'code',
                'choice_label' => 'name',
                'choice_translation_domain' => false,
            ])
            ->setDefined([
                'subject',
            ])
            ->setAllowedTypes('subject', ShippingSubjectInterface::class)
        ;
    }

    /**
     * {@inheritdoc}
     */
    public function buildView(FormView $view, FormInterface $form, array $options)
    {
        if (!isset($options['subject'])) {
            return;
        }

        $subject = $options['subject'];
        $shippingCosts = [];

        foreach ($view->vars['choices'] as $choiceView) {
            $method = $choiceView->data;

            if (!$method instanceof ShippingMethodInterface) {
                throw new UnexpectedTypeException($method, ShippingMethodInterface::class);
            }

            $calculator = $this->calculators->get($method->getCalculator());
            $shippingCosts[$choiceView->value] = $calculator->calculate($subject, $method->getConfiguration());
        }

        $view->vars['shipping_costs'] = $shippingCosts;
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
        return 'sylius_shipping_method_choice';
    }
}
