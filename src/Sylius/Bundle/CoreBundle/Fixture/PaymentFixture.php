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
use Sylius\Abstraction\StateMachine\StateMachineInterface;
use Sylius\Bundle\FixturesBundle\Fixture\AbstractFixture;
use Sylius\Component\Core\Model\PaymentInterface;
use Sylius\Component\Core\Repository\PaymentRepositoryInterface;
use Sylius\Component\Payment\PaymentTransitions;
use Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition;
use Symfony\Component\OptionsResolver\OptionsResolver;

class PaymentFixture extends AbstractFixture
{
    private Generator $faker;

    private OptionsResolver $optionsResolver;

    /**
     * @param PaymentRepositoryInterface<PaymentInterface> $paymentRepository
     */
    public function __construct(
        private PaymentRepositoryInterface $paymentRepository,
        private StateMachineInterface $stateMachine,
        private ObjectManager $paymentManager,
    ) {
        $this->faker = Factory::create();

        $this->optionsResolver = new OptionsResolver();
        $this->configureOptions($this->optionsResolver);
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
        $this->stateMachine->apply($payment, PaymentTransitions::GRAPH, PaymentTransitions::TRANSITION_COMPLETE);

        $this->paymentManager->persist($payment);
    }

    private function configureOptions(OptionsResolver $resolver): void
    {
        $resolver
            ->setDefault('percentage_completed', 0)
            ->setAllowedTypes('percentage_completed', 'int')
        ;
    }
}
