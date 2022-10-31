<?php

declare(strict_types=1);

namespace Sylius\Bundle\CoreBundle\Tests\Workflow;

use Sylius\Component\Core\Model\CatalogPromotion;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Workflow\StateMachine;

final class CatalogPromotionWorkflowTest extends KernelTestCase
{
    /** @test */
    public function it_is_a_state_machine(): void
    {
        $stateMachine = $this->getStateMachine();

        $this->assertInstanceOf(StateMachine::class, $stateMachine);
    }

    /** @test */
    public function it_has_states(): void
    {
        /** @var StateMachine $stateMachine */
        $stateMachine = $this->getStateMachine();

        $this->assertEquals([
            'active' => 'active',
            'inactive' => 'inactive',
            'processing' => 'processing',
        ], $stateMachine->getDefinition()->getPlaces());
    }

    /** @test */
    public function it_has_activate_transition(): void
    {
        /** @var StateMachine $stateMachine */
        $stateMachine = $this->getStateMachine();

        $this->assertEquals('activate', $stateMachine->getDefinition()->getTransitions()[0]->getName());
        $this->assertEquals(['processing'], $stateMachine->getDefinition()->getTransitions()[0]->getFroms());
        $this->assertEquals(['active'], $stateMachine->getDefinition()->getTransitions()[0]->getTos());
    }

    /** @test */
    public function it_has_deactivate_transition(): void
    {
        /** @var StateMachine $stateMachine */
        $stateMachine = $this->getStateMachine();

        $this->assertEquals('deactivate', $stateMachine->getDefinition()->getTransitions()[1]->getName());
        $this->assertEquals(['processing'], $stateMachine->getDefinition()->getTransitions()[1]->getFroms());
        $this->assertEquals(['inactive'], $stateMachine->getDefinition()->getTransitions()[1]->getTos());
    }

    /** @test */
    public function it_has_process_transition(): void
    {
        /** @var StateMachine $stateMachine */
        $stateMachine = $this->getStateMachine();

        $this->assertEquals('process', $stateMachine->getDefinition()->getTransitions()[2]->getName());
        $this->assertEquals(['inactive'], $stateMachine->getDefinition()->getTransitions()[2]->getFroms());
        $this->assertEquals(['processing'], $stateMachine->getDefinition()->getTransitions()[2]->getTos());

        $this->assertEquals('process', $stateMachine->getDefinition()->getTransitions()[3]->getName());
        $this->assertEquals(['active'], $stateMachine->getDefinition()->getTransitions()[3]->getFroms());
        $this->assertEquals(['processing'], $stateMachine->getDefinition()->getTransitions()[3]->getTos());
    }

    /** @test */
    public function it_can_pass_from_processing_to_active(): void
    {
        /** @var StateMachine $stateMachine */
        $stateMachine = $this->getStateMachine();

        $catalogPromotion = new CatalogPromotion();
        $catalogPromotion->setState('processing');

        $stateMachine->apply($catalogPromotion, 'activate');
        $this->assertEquals('active', $catalogPromotion->getState());
    }

    /** @test */
    public function it_can_pass_from_processing_to_inactive(): void
    {
        /** @var StateMachine $stateMachine */
        $stateMachine = $this->getStateMachine();

        $catalogPromotion = new CatalogPromotion();
        $catalogPromotion->setState('processing');

        $stateMachine->apply($catalogPromotion, 'deactivate');
        $this->assertEquals('inactive', $catalogPromotion->getState());
    }

    /** @test */
    public function it_can_pass_from_inactive_to_processing(): void
    {
        /** @var StateMachine $stateMachine */
        $stateMachine = $this->getStateMachine();

        $catalogPromotion = new CatalogPromotion();
        $catalogPromotion->setState('inactive');

        $stateMachine->apply($catalogPromotion, 'process');
        $this->assertEquals('processing', $catalogPromotion->getState());
    }

    /** @test */
    public function it_can_pass_from_active_to_processing(): void
    {
        /** @var StateMachine $stateMachine */
        $stateMachine = $this->getStateMachine();

        $catalogPromotion = new CatalogPromotion();
        $catalogPromotion->setState('active');

        $stateMachine->apply($catalogPromotion, 'process');
        $this->assertEquals('processing', $catalogPromotion->getState());
    }

    private function getStateMachine(): object
    {
        return self::getContainer()->get('state_machine.sylius_catalog_promotion');
    }
}
