<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Sylius Sp. z o.o.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Sylius\Bundle\ShippingBundle\Form\Type;

use Sylius\Component\Registry\ServiceRegistryInterface;
use Sylius\Component\Resource\Repository\RepositoryInterface;
use Sylius\Component\Shipping\Calculator\CalculatorInterface;
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

final class ShippingMethodChoiceType extends AbstractType
{
    public function __construct(
        private ShippingMethodsResolverInterface $shippingMethodsResolver,
        private ServiceRegistryInterface $calculators,
        private RepositoryInterface $repository,
    ) {
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        if ($options['multiple']) {
            $builder->addModelTransformer(new CollectionToArrayTransformer());
        }
    }

    public function configureOptions(OptionsResolver $resolver): void
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

    public function buildView(FormView $view, FormInterface $form, array $options): void
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

            /** @var CalculatorInterface $calculator */
            $calculator = $this->calculators->get($method->getCalculator());
            $shippingCosts[$choiceView->value] = $calculator->calculate($subject, $method->getConfiguration());
        }

        $view->vars['shipping_costs'] = $shippingCosts;
    }

    public function getParent(): string
    {
        return ChoiceType::class;
    }

    public function getBlockPrefix(): string
    {
        return 'sylius_shipping_method_choice';
    }
}
