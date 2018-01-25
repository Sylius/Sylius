<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Sylius\Bundle\PaymentBundle\Form\Type;

use Sylius\Component\Payment\Model\PaymentInterface;
use Sylius\Component\Payment\Resolver\PaymentMethodsResolverInterface;
use Sylius\Component\Resource\Repository\RepositoryInterface;
use Symfony\Bridge\Doctrine\Form\DataTransformer\CollectionToArrayTransformer;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\Options;
use Symfony\Component\OptionsResolver\OptionsResolver;

final class PaymentMethodChoiceType extends AbstractType
{
    /**
     * @var PaymentMethodsResolverInterface
     */
    private $paymentMethodsResolver;

    /**
     * @var RepositoryInterface
     */
    private $paymentMethodRepository;

    /**
     * @param PaymentMethodsResolverInterface $paymentMethodsResolver
     * @param RepositoryInterface $paymentMethodRepository
     */
    public function __construct(
        PaymentMethodsResolverInterface $paymentMethodsResolver,
        RepositoryInterface $paymentMethodRepository
    ) {
        $this->paymentMethodsResolver = $paymentMethodsResolver;
        $this->paymentMethodRepository = $paymentMethodRepository;
    }

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        if ($options['multiple']) {
            $builder->addModelTransformer(new CollectionToArrayTransformer());
        }
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver
            ->setDefaults([
                'choices' => function (Options $options) {
                    if (isset($options['subject'])) {
                        return $this->paymentMethodsResolver->getSupportedMethods($options['subject']);
                    }

                    return $this->paymentMethodRepository->findAll();
                },
                'choice_value' => 'code',
                'choice_label' => 'name',
                'choice_translation_domain' => false,
            ])
            ->setDefined([
                'subject',
            ])
            ->setAllowedTypes('subject', PaymentInterface::class)
        ;
    }

    /**
     * {@inheritdoc}
     */
    public function getParent(): string
    {
        return ChoiceType::class;
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix(): string
    {
        return 'sylius_payment_method_choice';
    }
}
