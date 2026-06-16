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

namespace Tests\Sylius\Bundle\CoreBundle\DependencyInjection\Compiler;

use Matthias\SymfonyDependencyInjectionTest\PhpUnit\AbstractCompilerPassTestCase;
use PHPUnit\Framework\Attributes\CoversClass;
use Sylius\Bundle\CoreBundle\DependencyInjection\Compiler\OverridePromotionServicesPass;
use Sylius\Component\Core\Promotion\Action\ChannelAwarePromotionApplicator;
use Sylius\Component\Core\Promotion\Checker\Eligibility\ChannelAwarePromotionRulesEligibilityChecker;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;

#[CoversClass(OverridePromotionServicesPass::class)]
final class OverridePromotionServicesPassTest extends AbstractCompilerPassTestCase
{
    protected function registerCompilerPass(ContainerBuilder $container): void
    {
        $container->addCompilerPass(new OverridePromotionServicesPass());
    }

    public function testItReplacesClassOfRulesEligibilityChecker(): void
    {
        $this->setDefinition('sylius.checker.promotion.rules_eligibility', new Definition(\stdClass::class));

        $this->compile();

        $this->assertContainerBuilderHasService(
            'sylius.checker.promotion.rules_eligibility',
            ChannelAwarePromotionRulesEligibilityChecker::class,
        );
    }

    public function testItReplacesClassOfPromotionApplicator(): void
    {
        $this->setDefinition('sylius.action.applicator.promotion', new Definition(\stdClass::class));

        $this->compile();

        $this->assertContainerBuilderHasService(
            'sylius.action.applicator.promotion',
            ChannelAwarePromotionApplicator::class,
        );
    }

    public function testItDoesNothingIfRulesEligibilityCheckerServiceDoesNotExist(): void
    {
        $this->compile();

        $this->assertContainerBuilderNotHasService('sylius.checker.promotion.rules_eligibility');
    }

    public function testItDoesNothingIfPromotionApplicatorServiceDoesNotExist(): void
    {
        $this->compile();

        $this->assertContainerBuilderNotHasService('sylius.action.applicator.promotion');
    }
}
