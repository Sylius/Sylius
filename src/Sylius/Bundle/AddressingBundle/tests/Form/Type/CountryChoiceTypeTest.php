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
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\MockObject\MockObject;
use Sylius\Bundle\AddressingBundle\Form\Type\CountryChoiceType;
use Sylius\Component\Addressing\Model\CountryInterface;
use Sylius\Resource\Doctrine\Persistence\RepositoryInterface;
use Symfony\Component\Form\ChoiceList\View\ChoiceView;
use Symfony\Component\Form\PreloadedExtension;
use Symfony\Component\Form\Test\TypeTestCase;

final class CountryChoiceTypeTest extends TypeTestCase
{
    private MockObject&RepositoryInterface $countryRepository;

    private CountryInterface&MockObject $france;

    private CountryInterface&MockObject $poland;

    private CountryInterface&MockObject $austria;

    protected function setUp(): void
    {
        $this->countryRepository = $this->createMock(RepositoryInterface::class);

        $this->france = $this->createMock(CountryInterface::class);
        $this->france->method('getCode')->willReturn('FR');
        $this->france->method('getName')->willReturn('France');

        $this->poland = $this->createMock(CountryInterface::class);
        $this->poland->method('getCode')->willReturn('PL');
        $this->poland->method('getName')->willReturn('Poland');

        $this->austria = $this->createMock(CountryInterface::class);
        $this->austria->method('getCode')->willReturn('AT');
        $this->austria->method('getName')->willReturn('Austria');
        $this->austria->method('isEnabled')->willReturn(false);

        parent::setUp();
    }

    protected function getExtensions(): array
    {
        $type = new CountryChoiceType($this->countryRepository);

        return [
            new PreloadedExtension([$type], []),
        ];
    }

    #[Test]
    public function it_returns_only_enabled_countries_by_default(): void
    {
        $this->countryRepository
            ->expects($this->once())
            ->method('findBy')
            ->with(['enabled' => true])
            ->willReturn([
                $this->france,
                $this->poland,
            ]);

        $this->assertChoicesLabels(['France', 'Poland']);
    }

    #[Test]
    public function it_returns_all_countries_when_option_enabled_is_false(): void
    {
        $this->countryRepository
            ->expects($this->once())
            ->method('findAll')
            ->willReturn([
                $this->france,
                $this->poland,
                $this->austria,
            ]);

        $this->assertChoicesLabels(['Austria', 'France', 'Poland'], ['enabled' => false]);
    }

    #[Test]
    public function it_returns_enabled_countries_in_an_alphabetical_order(): void
    {
        $this->countryRepository
            ->expects($this->once())
            ->method('findBy')
            ->with(['enabled' => true])
            ->willReturn([
                $this->poland,
                $this->france,
            ]);

        $this->assertChoicesLabels(['France', 'Poland']);
    }

    #[Test]
    public function it_returns_all_countries_in_an_alphabetical_order(): void
    {
        $this->countryRepository
            ->expects($this->once())
            ->method('findAll')
            ->willReturn([
                $this->poland,
                $this->france,
                $this->austria,
            ]);

        $this->assertChoicesLabels(['Austria', 'France', 'Poland'], ['enabled' => false]);
    }

    #[Test]
    public function it_returns_all_filtered_out_countries(): void
    {
        $this->countryRepository
            ->expects($this->once())
            ->method('findAll')
            ->willReturn([
                $this->france,
                $this->poland,
                $this->austria,
            ]);

        $this->assertChoicesLabels(['Poland'], ['choice_filter' => static fn (?CountryInterface $country): bool => $country !== null && $country->getName() === 'Poland', 'enabled' => false]);
    }

    #[Test]
    public function it_returns_enabled_filtered_out_countries(): void
    {
        $this->countryRepository
            ->expects($this->once())
            ->method('findBy')
            ->with(['enabled' => true])
            ->willReturn([
                $this->france,
                $this->poland,
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
