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

namespace Sylius\Bundle\PaymentBundle\Form\Type;

use Sylius\Bundle\PaymentBundle\Generator\GatewayConfigNameGeneratorInterface;
use Sylius\Bundle\PaymentBundle\Validator\GroupsGenerator\PaymentMethodGroupsGeneratorInterface;
use Sylius\Bundle\ResourceBundle\Form\EventSubscriber\AddCodeFormSubscriber;
use Sylius\Bundle\ResourceBundle\Form\Type\AbstractResourceType;
use Sylius\Bundle\ResourceBundle\Form\Type\ResourceTranslationsType;
use Sylius\Component\Payment\Model\PaymentMethodInterface;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

final class PaymentMethodType extends AbstractResourceType
{
    public function __construct(
        string $dataClass,
        array $validationGroups,
        private readonly PaymentMethodGroupsGeneratorInterface $paymentMethodGroupsGenerator,
        private readonly GatewayConfigNameGeneratorInterface $gatewayConfigNameGenerator,
    ) {
        parent::__construct($dataClass, $validationGroups);
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $gatewayFactory = $options['data']->getGatewayConfig();

        $builder
            ->add('translations', ResourceTranslationsType::class, [
                'entry_type' => PaymentMethodTranslationType::class,
                'label' => 'sylius.form.payment_method.name',
            ])
            ->add('position', IntegerType::class, [
                'required' => false,
                'label' => 'sylius.form.shipping_method.position',
            ])
            ->add('enabled', CheckboxType::class, [
                'required' => false,
                'label' => 'sylius.form.payment_method.enabled',
            ])
            ->add('gatewayConfig', GatewayConfigType::class, [
                'label' => false,
                'data' => $gatewayFactory,
            ])
            ->addEventSubscriber(new AddCodeFormSubscriber())
            ->addEventListener(FormEvents::SUBMIT, function (FormEvent $event): void {
                $paymentMethod = $event->getData();

                if (!$paymentMethod instanceof PaymentMethodInterface) {
                    return;
                }

                $gatewayConfig = $paymentMethod->getGatewayConfig();
                /** @var string|null $gatewayName */
                $gatewayName = $gatewayConfig->getGatewayName();

                if (null === $gatewayName && null !== $paymentMethod->getCode()) {
                    $gatewayConfig->setGatewayName($this->gatewayConfigNameGenerator->generate($paymentMethod));
                }
            })
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'validation_groups' => function (FormInterface $form): array {
                $data = $form->getData();

                return $this->paymentMethodGroupsGenerator->__invoke($data);
            },
        ]);
    }

    public function getBlockPrefix(): string
    {
        return 'sylius_payment_method';
    }
}
