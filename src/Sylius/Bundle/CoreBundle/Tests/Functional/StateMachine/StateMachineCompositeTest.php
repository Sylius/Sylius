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

use Sylius\Bundle\CoreBundle\Application\Model\BlogPost;
use Sylius\Bundle\CoreBundle\Application\Model\Comment;
use Sylius\Bundle\CoreBundle\StateMachine\Exception\StateMachineExecutionException;
use Sylius\Bundle\CoreBundle\StateMachine\StateMachineInterface;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

final class StateMachineCompositeTest extends KernelTestCase
{
    /** @test */
    public function it_returns_whether_a_transition_can_be_applied_basing_on_the_state_machine_adapter_with_the_highest_priority(): void
    {
        $stateMachine = $this->getStateMachine();

        $blogPost = new BlogPost();

        $this->assertTrue($stateMachine->can($blogPost, 'app_blog_post', 'publish'));
        $this->assertFalse($stateMachine->can($blogPost, 'app_blog_post', 'post'));
    }

    /** @test */
    public function it_returns_whether_a_transition_can_be_applied_fallback_to_the_state_machine_adapters_with_the_lower_priority(): void
    {
        $stateMachine = $this->getStateMachine();

        $comment = new Comment();

        $this->assertTrue($stateMachine->can($comment, 'app_comment', 'publish'));
    }

    /** @test */
    public function it_throws_the_last_exception_thrown_by_the_state_machine_adapters(): void
    {
        $stateMachine = $this->getStateMachine();

        $comment = new Comment();

        $this->expectException(StateMachineExecutionException::class);
        $this->expectExceptionMessage('Cannot create a state machine because the configuration for object "Sylius\Bundle\CoreBundle\Application\Model\Comment" with graph "app_blog_comment" does not exist');

        $stateMachine->apply($comment, 'app_blog_comment', 'publish');
    }

    private function getStateMachine(): StateMachineInterface
    {
        return self::getContainer()->get('sylius.state_machine.composite');
    }
}
