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

namespace Tests\Sylius\Bundle\CoreBundle\Functional\StateMachine;

use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use Sylius\Abstraction\StateMachine\StateMachineInterface;
use Sylius\Component\Core\Model\ProductReview;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

final class ProductReviewWorkflowTest extends KernelTestCase
{
    #[DataProvider('availableTransitionsForNewStatus')]
    #[Test]
    public function it_applies_all_available_transitions_for_new_status(string $transition, string $expectedStatus): void
    {
        $stateMachine = $this->getStateMachine();
        $subject = new ProductReview();
        $stateMachine->apply($subject, 'sylius_product_review', $transition);

        $this->assertSame($expectedStatus, $subject->getStatus());
    }

    public static function availableTransitionsForNewStatus(): iterable
    {
        yield ['accept', 'accepted'];
        yield ['reject', 'rejected'];
    }

    private function getStateMachine(): StateMachineInterface
    {
        return self::getContainer()->get('sylius_abstraction.state_machine.adapter.symfony_workflow');
    }
}
