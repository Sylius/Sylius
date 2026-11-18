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

namespace Tests\Sylius\Bundle\AddressingBundle\Form\Type;

use PHPUnit\Framework\Assert;
use PHPUnit\Framework\Attributes\AllowMockObjectsWithoutExpectations;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\MockObject\MockObject;
use Sylius\Bundle\AddressingBundle\Form\Type\ZoneChoiceType;
use Sylius\Component\Addressing\Model\Scope as AddressingScope;
use Sylius\Component\Addressing\Model\ZoneInterface;
use Sylius\Resource\Doctrine\Persistence\RepositoryInterface;
use Symfony\Component\Form\ChoiceList\View\ChoiceView;
use Symfony\Component\Form\PreloadedExtension;
use Symfony\Component\Form\Test\TypeTestCase;

#[AllowMockObjectsWithoutExpectations]
final class ZoneChoiceTypeTest extends TypeTestCase
{
    private MockObject&RepositoryInterface $zoneRepository;

    private MockObject&ZoneInterface $zoneAllScopes;

    private MockObject&ZoneInterface $zoneTaxScope;

    private MockObject&ZoneInterface $zoneShippingScope;

    protected function setUp(): void
    {
        $this->zoneRepository = $this->createMock(RepositoryInterface::class);

        $zoneAllScopes = $this->createMock(ZoneInterface::class);
        $zoneAllScopes->method('getCode')->willReturn('all');
        $zoneAllScopes->method('getName')->willReturn('All');
        $this->zoneAllScopes = $zoneAllScopes;

        $zoneTaxScope = $this->createMock(ZoneInterface::class);
        $zoneTaxScope->method('getCode')->willReturn('tax');
        $zoneTaxScope->method('getName')->willReturn('Tax');
        $this->zoneTaxScope = $zoneTaxScope;

        $zoneShippingScope = $this->createMock(ZoneInterface::class);
        $zoneShippingScope->method('getCode')->willReturn('shipping');
        $zoneShippingScope->method('getName')->willReturn('Shipping');
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

        $type = new ZoneChoiceType($this->zoneRepository, $scopeTypes);

        return [
            new PreloadedExtension([$type], []),
        ];
    }

    #[Test]
    public function it_returns_all_scopes_by_default(): void
    {
        $this->zoneRepository->method('findBy')->with([])->willReturn([
            $this->zoneAllScopes,
            $this->zoneTaxScope,
            $this->zoneShippingScope,
        ]);

        $this->assertChoicesLabels(['All', 'Tax', 'Shipping']);
    }

    #[Test]
    public function it_returns_all_scopes_when_zone_scope_set_to_all(): void
    {
        $this->zoneRepository->method('findBy')->with([])->willReturn([
            $this->zoneAllScopes,
            $this->zoneTaxScope,
            $this->zoneShippingScope,
        ]);

        $this->assertChoicesLabels(['All', 'Tax', 'Shipping'], ['zone_scope' => AddressingScope::ALL]);
    }

    #[Test]
    public function it_returns_tax_scopes_when_zone_scope_set_to_tax(): void
    {
        $this->zoneRepository->method('findBy')->with(['scope' => ['tax', AddressingScope::ALL]])->willReturn([
            $this->zoneAllScopes,
            $this->zoneTaxScope,
        ]);

        $this->assertChoicesLabels(['All', 'Tax'], ['zone_scope' => 'tax']);
    }

    #[Test]
    public function it_returns_shipping_scopes_when_zone_scope_set_to_shipping(): void
    {
        $this->zoneRepository->method('findBy')->with(['scope' => ['shipping', AddressingScope::ALL]])->willReturn([
            $this->zoneAllScopes,
            $this->zoneShippingScope,
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
