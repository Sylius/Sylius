<?php

declare(strict_types=1);

namespace Sylius\Bundle\CoreBundle\Tests\Workflow;

use SM\Factory\Factory;
use Sylius\Component\Core\Model\CatalogPromotion;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Workflow\StateMachine;

final class CatalogPromotionWorkflowTest extends KernelTestCase
{
    /** @test */
    public function it_is_a_state_machine(): void
    {
        $stateMachine = $this->getSymfonyStateMachine();

        $this->assertInstanceOf(StateMachine::class, $stateMachine);
    }

    /** @test */
    public function it_has_states(): void
    {
        /** @var StateMachine $symfonyStateMachine */
        $symfonyStateMachine = $this->getSymfonyStateMachine();

        $winzouStateMachineConfig = $this->getWinzouStateMachineConfig();

        $this->assertEquals([
            'active' => 'active',
            'inactive' => 'inactive',
            'processing' => 'processing',
        ], $symfonyStateMachine->getDefinition()->getPlaces());

        $this->assertEquals([
            'active',
            'inactive',
            'processing'
        ], $winzouStateMachineConfig['states']);
    }

    /** @test */
    public function it_has_activate_transition(): void
    {
        /** @var StateMachine $symfonyStateMachine */
        $symfonyStateMachine = $this->getSymfonyStateMachine();

        $winzouStateMachineConfig = $this->getWinzouStateMachineConfig();

        $this->assertEquals('activate', $symfonyStateMachine->getDefinition()->getTransitions()[0]->getName());
        $this->assertEquals(['processing'], $symfonyStateMachine->getDefinition()->getTransitions()[0]->getFroms());
        $this->assertEquals(['active'], $symfonyStateMachine->getDefinition()->getTransitions()[0]->getTos());

        $this->assertEquals([
            'from' => ['processing'],
            'to' => 'active',
        ], $winzouStateMachineConfig['transitions']['activate']);
    }

    /** @test */
    public function it_has_deactivate_transition(): void
    {
        /** @var StateMachine $symfonyStateMachine */
        $symfonyStateMachine = $this->getSymfonyStateMachine();

        $winzouStateMachineConfig = $this->getWinzouStateMachineConfig();

        $this->assertEquals('deactivate', $symfonyStateMachine->getDefinition()->getTransitions()[1]->getName());
        $this->assertEquals(['processing'], $symfonyStateMachine->getDefinition()->getTransitions()[1]->getFroms());
        $this->assertEquals(['inactive'], $symfonyStateMachine->getDefinition()->getTransitions()[1]->getTos());

        $this->assertEquals([
            'from' => ['processing'],
            'to' => 'inactive',
        ], $winzouStateMachineConfig['transitions']['deactivate']);
    }

    /** @test */
    public function it_has_process_transition(): void
    {
        /** @var StateMachine $symfonyStateMachine */
        $symfonyStateMachine = $this->getSymfonyStateMachine();

        $winzouStateMachineConfig = $this->getWinzouStateMachineConfig();

        $this->assertEquals('process', $symfonyStateMachine->getDefinition()->getTransitions()[2]->getName());
        $this->assertEquals(['inactive'], $symfonyStateMachine->getDefinition()->getTransitions()[2]->getFroms());
        $this->assertEquals(['processing'], $symfonyStateMachine->getDefinition()->getTransitions()[2]->getTos());

        $this->assertEquals('process', $symfonyStateMachine->getDefinition()->getTransitions()[3]->getName());
        $this->assertEquals(['active'], $symfonyStateMachine->getDefinition()->getTransitions()[3]->getFroms());
        $this->assertEquals(['processing'], $symfonyStateMachine->getDefinition()->getTransitions()[3]->getTos());

        $this->assertEquals([
            'from' => ['inactive', 'active'],
            'to' => 'processing',
        ], $winzouStateMachineConfig['transitions']['process']);
    }

    /** @test */
    public function it_can_pass_from_processing_to_active(): void
    {
        /** @var StateMachine $symfonyStateMachine */
        $symfonyStateMachine = $this->getSymfonyStateMachine();

        /** @var Factory $winzouStateMachine */
        $winzouStateMachine = $this->getWinzouStateMachine();

        $catalogPromotion = new CatalogPromotion();
        $catalogPromotion->setState('processing');

        $symfonyStateMachine->apply($catalogPromotion, 'activate');
        $this->assertEquals('active', $catalogPromotion->getState());

        $catalogPromotion = new CatalogPromotion();
        $catalogPromotion->setState('processing');

        $winzouStateMachine->get($catalogPromotion, 'sylius_catalog_promotion')->apply('activate');
        $this->assertEquals('active', $catalogPromotion->getState());
    }

    /** @test */
    public function it_can_pass_from_processing_to_inactive(): void
    {
        /** @var StateMachine $symfonyStateMachine */
        $symfonyStateMachine = $this->getSymfonyStateMachine();

        /** @var Factory $winzouStateMachine */
        $winzouStateMachine = $this->getWinzouStateMachine();

        $catalogPromotion = new CatalogPromotion();
        $catalogPromotion->setState('processing');

        $symfonyStateMachine->apply($catalogPromotion, 'deactivate');
        $this->assertEquals('inactive', $catalogPromotion->getState());

        $catalogPromotion = new CatalogPromotion();
        $catalogPromotion->setState('processing');

        $winzouStateMachine->get($catalogPromotion, 'sylius_catalog_promotion')->apply('deactivate');
        $this->assertEquals('inactive', $catalogPromotion->getState());
    }

    /** @test */
    public function it_can_pass_from_inactive_to_processing(): void
    {
        /** @var StateMachine $symfonyStateMachine */
        $symfonyStateMachine = $this->getSymfonyStateMachine();

        /** @var Factory $winzouStateMachine */
        $winzouStateMachine = $this->getWinzouStateMachine();

        $catalogPromotion = new CatalogPromotion();
        $catalogPromotion->setState('inactive');

        $symfonyStateMachine->apply($catalogPromotion, 'process');
        $this->assertEquals('processing', $catalogPromotion->getState());

        $catalogPromotion = new CatalogPromotion();
        $catalogPromotion->setState('inactive');

        $winzouStateMachine->get($catalogPromotion, 'sylius_catalog_promotion')->apply('process');
        $this->assertEquals('processing', $catalogPromotion->getState());
    }

    /** @test */
    public function it_can_pass_from_active_to_processing(): void
    {
        /** @var StateMachine $symfonyStateMachine */
        $symfonyStateMachine = $this->getSymfonyStateMachine();

        /** @var Factory $winzouStateMachine */
        $winzouStateMachine = $this->getWinzouStateMachine();

        $catalogPromotion = new CatalogPromotion();
        $catalogPromotion->setState('active');

        $symfonyStateMachine->apply($catalogPromotion, 'process');
        $this->assertEquals('processing', $catalogPromotion->getState());

        $catalogPromotion = new CatalogPromotion();
        $catalogPromotion->setState('active');

        $winzouStateMachine->get($catalogPromotion, 'sylius_catalog_promotion')->apply('process');
        $this->assertEquals('processing', $catalogPromotion->getState());
    }

    private function getSymfonyStateMachine(): object
    {
        return self::getContainer()->get('state_machine.sylius_catalog_promotion');
    }

    private function getWinzouStateMachine(): object
    {
        return self::getContainer()->get('sm.factory');
    }

    private function getWinzouStateMachineConfig(): array
    {
        return self::getContainer()->getParameter('sm.configs')['sylius_catalog_promotion'];
    }
}
