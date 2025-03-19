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

namespace Sylius\Bundle\CoreBundle\Tests\Functional\StateMachine;

use PHPUnit\Framework\MockObject\MockObject;
use Sylius\Abstraction\StateMachine\StateMachineInterface;
use Sylius\Component\Core\Model\PaymentInterface;
use Sylius\Component\Core\Model\PaymentMethodInterface;
use Sylius\Component\Payment\Model\PaymentRequest;
use Sylius\Component\Payment\Model\PaymentRequestInterface;
use Sylius\Component\Payment\PaymentRequestTransitions;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

final class PaymentRequestWorkflowTest extends KernelTestCase
{
    /** @var PaymentRequestInterface|MockObject */
    protected PaymentRequestInterface $paymentRequest;

    public function setUp(): void
    {
        parent::setUp();

        $this->paymentRequest = new PaymentRequest(
            $this->createMock(PaymentInterface::class),
            $this->createMock(PaymentMethodInterface::class),
        );
    }

    /**
     * @test
     *
     * @dataProvider availableTransitions
     */
    public function it_applies_all_available_transitions(
        string $fromState,
        string $transition,
        string $toState,
    ): void {
        $this->paymentRequest->setState($fromState);

        $stateMachine = $this->getStateMachine();
        $stateMachine->apply($this->paymentRequest, PaymentRequestTransitions::GRAPH, $transition);

        $this->assertSame($toState, $this->paymentRequest->getState());
    }

    public function availableTransitions(): iterable
    {
        yield [PaymentRequestInterface::STATE_NEW, PaymentRequestTransitions::TRANSITION_PROCESS, PaymentRequestInterface::STATE_PROCESSING];
        yield [PaymentRequestInterface::STATE_NEW, PaymentRequestTransitions::TRANSITION_COMPLETE, PaymentRequestInterface::STATE_COMPLETED];
        yield [PaymentRequestInterface::STATE_PROCESSING, PaymentRequestTransitions::TRANSITION_COMPLETE, PaymentRequestInterface::STATE_COMPLETED];
        yield [PaymentRequestInterface::STATE_NEW, PaymentRequestTransitions::TRANSITION_FAIL, PaymentRequestInterface::STATE_FAILED];
        yield [PaymentRequestInterface::STATE_PROCESSING, PaymentRequestTransitions::TRANSITION_FAIL, PaymentRequestInterface::STATE_FAILED];
        yield [PaymentRequestInterface::STATE_NEW, PaymentRequestTransitions::TRANSITION_CANCEL, PaymentRequestInterface::STATE_CANCELLED];
        yield [PaymentRequestInterface::STATE_PROCESSING, PaymentRequestTransitions::TRANSITION_CANCEL, PaymentRequestInterface::STATE_CANCELLED];
    }

    private function getStateMachine(): StateMachineInterface
    {
        return self::getContainer()->get('sylius_abstraction.state_machine.adapter.symfony_workflow');
    }
}
