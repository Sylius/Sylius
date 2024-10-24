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

use Sylius\Abstraction\StateMachine\StateMachineInterface;
use Sylius\Bundle\CoreBundle\Application\Model\BlogPost;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

final class StateMachineCompositeTest extends KernelTestCase
{
    /** @test */
    public function it_calls_a_method_on_a_default_state_machine_adapter_when_mapping_is_not_configured_for_a_given_graph(): void
    {
        $stateMachine = $this->getStateMachine();

        $subject = new BlogPost();

        $this->assertTrue($stateMachine->can($subject, 'app_blog_post', 'publish'));
    }

    private function getStateMachine(): StateMachineInterface
    {
        return self::getContainer()->get('sylius_abstraction.state_machine');
    }
}
