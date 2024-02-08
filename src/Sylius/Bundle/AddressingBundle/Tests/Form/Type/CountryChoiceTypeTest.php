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
use Prophecy\Prophecy\ObjectProphecy;
use Prophecy\Prophecy\ProphecyInterface;
use Sylius\Bundle\AddressingBundle\Form\Type\CountryChoiceType;
use Sylius\Component\Addressing\Model\CountryInterface;
use Sylius\Component\Resource\Repository\RepositoryInterface;
use Symfony\Component\Form\ChoiceList\View\ChoiceView;
use Symfony\Component\Form\PreloadedExtension;
use Symfony\Component\Form\Test\TypeTestCase;

final class CountryChoiceTypeTest extends TypeTestCase
{
    private ObjectProphecy $countryRepository;

    /** @var ProphecyInterface|CountryInterface */
    private $france;

    /** @var ProphecyInterface|CountryInterface */
    private $poland;

    /** @var ProphecyInterface|CountryInterface */
    private $austria;

    protected function setUp(): void
    {
        $this->countryRepository = $this->prophesize(RepositoryInterface::class);

        /** @var ProphecyInterface|CountryInterface $france */
        $france = $this->prophesize(CountryInterface::class);
        $france->getCode()->willReturn('FR');
        $france->getName()->willReturn('France');
        $this->france = $france;

        /** @var ProphecyInterface|CountryInterface $poland */
        $poland = $this->prophesize(CountryInterface::class);
        $poland->getCode()->willReturn('PL');
        $poland->getName()->willReturn('Poland');
        $this->poland = $poland;

        /** @var ProphecyInterface|CountryInterface $austria */
        $austria = $this->prophesize(CountryInterface::class);
        $austria->getCode()->willReturn('AT');
        $austria->getName()->willReturn('Austria');
        $austria->isEnabled()->willReturn(false);
        $this->austria = $austria;

        parent::setUp();
    }

    protected function getExtensions(): array
    {
        $type = new CountryChoiceType($this->countryRepository->reveal());

        return [
            new PreloadedExtension([$type], []),
        ];
    }

    /** @test */
    public function it_returns_only_enabled_countries_by_default(): void
    {
        $this->countryRepository->findBy(['enabled' => true])->willReturn([
            $this->france->reveal(),
            $this->poland->reveal(),
        ]);

        $this->assertChoicesLabels(['France', 'Poland']);
    }

    /** @test */
    public function it_returns_all_countries_when_option_enabled_is_false(): void
    {
        $this->countryRepository->findAll()->willReturn([
            $this->france->reveal(),
            $this->poland->reveal(),
            $this->austria->reveal(),
        ]);

        $this->assertChoicesLabels(['Austria', 'France', 'Poland'], ['enabled' => false]);
    }

    /** @test */
    public function it_returns_enabled_countries_in_an_alphabetical_order(): void
    {
        $this->countryRepository->findBy(['enabled' => true])->willReturn([
            $this->poland->reveal(),
            $this->france->reveal(),
        ]);

        $this->assertChoicesLabels(['France', 'Poland']);
    }

    /** @test */
    public function it_returns_all_countries_in_an_alphabetical_order(): void
    {
        $this->countryRepository->findAll()->willReturn([
            $this->poland->reveal(),
            $this->france->reveal(),
            $this->austria->reveal(),
        ]);

        $this->assertChoicesLabels(['Austria', 'France', 'Poland'], ['enabled' => false]);
    }

    /** @test */
    public function it_returns_all_filtered_out_countries(): void
    {
        $this->countryRepository->findAll()->willReturn([
            $this->france->reveal(),
            $this->poland->reveal(),
            $this->austria->reveal(),
        ]);

        $this->assertChoicesLabels(['Poland'], ['choice_filter' => static fn (?CountryInterface $country): bool => $country !== null && $country->getName() === 'Poland', 'enabled' => false]);
    }

    /** @test */
    public function it_returns_enabled_filtered_out_countries(): void
    {
        $this->countryRepository->findBy(['enabled' => true])->willReturn([
            $this->france->reveal(),
            $this->poland->reveal(),
        ]);

        $this->assertChoicesLabels(['Poland'], ['choice_filter' => static fn (?CountryInterface $country): bool => $country !== null && $country->getName() === 'Poland']);
    }

    private function assertChoicesLabels(array $expectedLabels, array $formConfiguration = []): void
    {
        $form = $this->factory->create(CountryChoiceType::class, null, $formConfiguration);
        $view = $form->createView();

        Assert::assertSame($expectedLabels, array_map(static fn (ChoiceView $choiceView): string => $choiceView->label, $view->vars['choices']));
    }
}
