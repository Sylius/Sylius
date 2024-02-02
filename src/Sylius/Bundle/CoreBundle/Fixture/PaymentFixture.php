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

namespace Sylius\Bundle\CoreBundle\Fixture;

use Doctrine\Persistence\ObjectManager;
use Faker\Factory;
use Faker\Generator;
use SM\Factory\FactoryInterface as StateMachineFactoryInterface;
use Sylius\Abstraction\StateMachine\StateMachineInterface;
use Sylius\Abstraction\StateMachine\WinzouStateMachineAdapter;
use Sylius\Bundle\FixturesBundle\Fixture\AbstractFixture;
use Sylius\Component\Core\Model\PaymentInterface;
use Sylius\Component\Core\Repository\PaymentRepositoryInterface;
use Sylius\Component\Payment\PaymentTransitions;
use Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition;
use Symfony\Component\OptionsResolver\OptionsResolver;

class_alias(PaymentFixture::class, '\Sylius\Bundle\CoreBundle\Fixture\Factory\PaymentFixture');

trigger_deprecation(
    'sylius/core-bundle',
    '1.13',
    'The "%s" class is deprecated and will be removed in Sylius 2.0. Use "%s" instead.',
    '\Sylius\Bundle\CoreBundle\Fixture\Factory\PaymentFixture',
    PaymentFixture::class,
);

class PaymentFixture extends AbstractFixture
{
    private Generator $faker;

    private OptionsResolver $optionsResolver;

    /**
     * @param PaymentRepositoryInterface<PaymentInterface> $paymentRepository
     */
    public function __construct(
        private PaymentRepositoryInterface $paymentRepository,
        private StateMachineFactoryInterface|StateMachineInterface $stateMachineFactory,
        private ObjectManager $paymentManager,
    ) {
        $this->faker = Factory::create();

        $this->optionsResolver = new OptionsResolver();
        $this->configureOptions($this->optionsResolver);

        if ($this->stateMachineFactory instanceof StateMachineFactoryInterface) {
            trigger_deprecation(
                'sylius/core-bundle',
                '1.13',
                sprintf(
                    'Passing an instance of "%s" as the second argument is deprecated. It will accept only instances of "%s" in Sylius 2.0.',
                    StateMachineFactoryInterface::class,
                    StateMachineInterface::class,
                ),
            );
        }
    }

    public function getName(): string
    {
        return 'payments';
    }

    /**
     * @param string[] $options
     */
    public function load(array $options): void
    {
        $options = $this->optionsResolver->resolve($options);

        $payments = $this->paymentRepository->findAll();

        /** @var PaymentInterface $payment */
        foreach ($payments as $payment) {
            if ($this->faker->boolean($options['percentage_completed'])) {
                $this->completePayment($payment);
            }
        }

        $this->paymentManager->flush();
    }

    protected function configureOptionsNode(ArrayNodeDefinition $optionsNode): void
    {
        $optionsNode
            ->children()
                ->integerNode('percentage_completed')->isRequired()->min(0)->max(100)->end()
        ;
    }

    private function completePayment(PaymentInterface $payment): void
    {
        $this->getStateMachine()->apply($payment, PaymentTransitions::GRAPH, PaymentTransitions::TRANSITION_COMPLETE);

        $this->paymentManager->persist($payment);
    }

    private function configureOptions(OptionsResolver $resolver): void
    {
        $resolver
            ->setDefault('percentage_completed', 0)
            ->setAllowedTypes('percentage_completed', 'int')
        ;
    }

    private function getStateMachine(): StateMachineInterface
    {
        if ($this->stateMachineFactory instanceof StateMachineFactoryInterface) {
            return new WinzouStateMachineAdapter($this->stateMachineFactory);
        }

        return $this->stateMachineFactory;
    }
}
