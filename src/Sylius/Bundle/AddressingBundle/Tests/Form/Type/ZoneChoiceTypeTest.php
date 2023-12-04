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

namespace Sylius\Bundle\AddressingBundle\Tests\Form\Type;

use PHPUnit\Framework\Assert;
use Prophecy\PhpUnit\ProphecyTrait;
use Prophecy\Prophecy\ObjectProphecy;
use Prophecy\Prophecy\ProphecyInterface;
use Sylius\Bundle\AddressingBundle\Form\Type\ZoneChoiceType;
use Sylius\Component\Addressing\Model\Scope as AddressingScope;
use Sylius\Component\Addressing\Model\ZoneInterface;
use Sylius\Component\Resource\Repository\RepositoryInterface;
use Symfony\Component\Form\ChoiceList\View\ChoiceView;
use Symfony\Component\Form\PreloadedExtension;
use Symfony\Component\Form\Test\TypeTestCase;

final class ZoneChoiceTypeTest extends TypeTestCase
{
    use ProphecyTrait;

    private ObjectProphecy $zoneRepository;

    /** @var ProphecyInterface|ZoneInterface */
    private $zoneAllScopes;

    /** @var ProphecyInterface|ZoneInterface */
    private $zoneTaxScope;

    /** @var ProphecyInterface|ZoneInterface */
    private $zoneShippingScope;

    protected function setUp(): void
    {
        $this->zoneRepository = $this->prophesize(RepositoryInterface::class);

        /** @var ProphecyInterface|ZoneInterface $zoneAllScopes */
        $zoneAllScopes = $this->prophesize(ZoneInterface::class);
        $zoneAllScopes->getCode()->willReturn('all');
        $zoneAllScopes->getName()->willReturn('All');
        $this->zoneAllScopes = $zoneAllScopes;

        /** @var ProphecyInterface|ZoneInterface $zoneTaxScope */
        $zoneTaxScope = $this->prophesize(ZoneInterface::class);
        $zoneTaxScope->getCode()->willReturn('tax');
        $zoneTaxScope->getName()->willReturn('Tax');
        $this->zoneTaxScope = $zoneTaxScope;

        /** @var ProphecyInterface|ZoneInterface $zoneShippingScope */
        $zoneShippingScope = $this->prophesize(ZoneInterface::class);
        $zoneShippingScope->getCode()->willReturn('shipping');
        $zoneShippingScope->getName()->willReturn('Shipping');
        $this->zoneShippingScope = $zoneShippingScope;

        parent::setUp();
    }

    protected function getExtensions(): array
    {
        $scopeTypes = [
            AddressingScope::ALL => 'All',
            'tax' => 'Tax',
            'shipping' => 'Shipping',
        ];

        $type = new ZoneChoiceType($this->zoneRepository->reveal(), $scopeTypes);

        return [
            new PreloadedExtension([$type], []),
        ];
    }

    /**
     * @test
     */
    public function it_returns_all_scopes_by_default(): void
    {
        $this->zoneRepository->findBy([])->willReturn([
            $this->zoneAllScopes->reveal(),
            $this->zoneTaxScope->reveal(),
            $this->zoneShippingScope->reveal(),
        ]);

        $this->assertChoicesLabels(['All', 'Tax', 'Shipping']);
    }

    /**
     * @test
     */
    public function it_returns_all_scopes_when_zone_scope_set_to_all(): void
    {
        $this->zoneRepository->findBy([])->willReturn([
            $this->zoneAllScopes->reveal(),
            $this->zoneTaxScope->reveal(),
            $this->zoneShippingScope->reveal(),
        ]);

        $this->assertChoicesLabels(['All', 'Tax', 'Shipping'], ['zone_scope' => AddressingScope::ALL]);
    }

    /**
     * @test
     */
    public function it_returns_tax_scopes_when_zone_scope_set_to_tax(): void
    {
        $this->zoneRepository->findBy(['scope' => ['tax', AddressingScope::ALL]])->willReturn([
            $this->zoneAllScopes->reveal(),
            $this->zoneTaxScope->reveal(),
        ]);

        $this->assertChoicesLabels(['All', 'Tax'], ['zone_scope' => 'tax']);
    }

    /**
     * @test
     */
    public function it_returns_shipping_scopes_when_zone_scope_set_to_shipping(): void
    {
        $this->zoneRepository->findBy(['scope' => ['shipping', AddressingScope::ALL]])->willReturn([
            $this->zoneAllScopes->reveal(),
            $this->zoneShippingScope->reveal(),
        ]);

        $this->assertChoicesLabels(['All', 'Shipping'], ['zone_scope' => 'shipping']);
    }

    private function assertChoicesLabels(array $expectedLabels, array $formConfiguration = []): void
    {
        $form = $this->factory->create(ZoneChoiceType::class, null, $formConfiguration);
        $view = $form->createView();

        Assert::assertSame($expectedLabels, array_map(fn (ChoiceView $choiceView): string => $choiceView->label, $view->vars['choices']));
    }
}
