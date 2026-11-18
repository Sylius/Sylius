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

namespace Tests\Sylius\Bundle\ProductBundle\Form\Type;

use PHPUnit\Framework\MockObject\MockObject;
use Sylius\Bundle\ProductBundle\Form\Type\ProductAttributeChoiceType;
use Sylius\Component\Attribute\Model\AttributeInterface;
use Sylius\Resource\Doctrine\Persistence\RepositoryInterface;
use Symfony\Component\Form\PreloadedExtension;
use Symfony\Component\Form\Test\TypeTestCase;
use Symfony\Contracts\Service\ResetInterface;

final class ProductAttributeChoiceTypeTest extends TypeTestCase
{
    private MockObject&RepositoryInterface $attributeRepository;

    private ProductAttributeChoiceType $type;

    public function testItOffersEveryAttribute(): void
    {
        $this->attributeRepository->method('findAll')->willReturn([$this->attribute('brand')]);

        $form = $this->factory->create(ProductAttributeChoiceType::class);

        $this->assertSame(['brand'], $this->offeredCodes($form->getConfig()->getOption('choices')));
    }

    public function testItOffersAnAttributeCreatedSinceTheLastReset(): void
    {
        $existing = $this->attribute('brand');
        $created = $this->attribute('probe_attr');

        $calls = 0;
        $this->attributeRepository->method('findAll')->willReturnCallback(
            function () use (&$calls, $existing, $created): array {
                return ++$calls === 1 ? [$existing] : [$existing, $created];
            },
        );

        $this->factory->create(ProductAttributeChoiceType::class);

        $this->assertInstanceOf(
            ResetInterface::class,
            $this->type,
            'The type must be resettable, otherwise nothing refreshes its attribute list between requests.',
        );
        $this->type->reset();

        $form = $this->factory->create(ProductAttributeChoiceType::class);

        $this->assertContains(
            'probe_attr',
            $this->offeredCodes($form->getConfig()->getOption('choices')),
            'The form was built from the attribute list of a previous request.',
        );
    }

    public function testItQueriesTheAttributesOnlyOnceBetweenResets(): void
    {
        $this->attributeRepository->expects($this->once())->method('findAll')->willReturn([$this->attribute('brand')]);

        $this->factory->create(ProductAttributeChoiceType::class);
        $this->factory->create(ProductAttributeChoiceType::class);
    }

    protected function setUp(): void
    {
        $this->attributeRepository = $this->createMock(RepositoryInterface::class);
        $this->type = new ProductAttributeChoiceType($this->attributeRepository);

        parent::setUp();
    }

    protected function getExtensions(): array
    {
        return [new PreloadedExtension([$this->type], [])];
    }

    /** @param iterable<AttributeInterface> $choices */
    private function offeredCodes(iterable $choices): array
    {
        $codes = [];
        foreach ($choices as $choice) {
            $codes[] = $choice->getCode();
        }

        return $codes;
    }

    private function attribute(string $code): AttributeInterface&MockObject
    {
        $attribute = $this->createMock(AttributeInterface::class);
        $attribute->method('getCode')->willReturn($code);
        $attribute->method('getName')->willReturn($code);

        return $attribute;
    }
}
