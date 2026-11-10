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
use Sylius\Bundle\ProductBundle\Form\Type\ProductAssociationsType;
use Sylius\Component\Product\Model\ProductAssociationTypeInterface;
use Sylius\Resource\Doctrine\Persistence\RepositoryInterface;
use Symfony\Component\Form\DataTransformerInterface;
use Symfony\Component\Form\PreloadedExtension;
use Symfony\Component\Form\Test\TypeTestCase;

final class ProductAssociationsTypeTest extends TypeTestCase
{
    private MockObject&RepositoryInterface $associationTypeRepository;

    private DataTransformerInterface&MockObject $transformer;

    public function testItBuildsAnEntryForAnAssociationTypeCreatedSinceTheFirstForm(): void
    {
        $existing = $this->associationType('similar_products');
        $created = $this->associationType('probe_assoc');

        $calls = 0;
        $this->associationTypeRepository->method('findAll')->willReturnCallback(
            function () use (&$calls, $existing, $created): array {
                return ++$calls === 1 ? [$existing] : [$existing, $created];
            },
        );

        $this->factory->create(ProductAssociationsType::class);
        $form = $this->factory->create(ProductAssociationsType::class);

        $this->assertTrue(
            $form->has('probe_assoc'),
            'The form was built from the association types of an earlier form.',
        );
    }

    public function testItBuildsAnEntryForEveryAssociationType(): void
    {
        $this->associationTypeRepository->method('findAll')->willReturn([$this->associationType('similar_products')]);

        $form = $this->factory->create(ProductAssociationsType::class);

        $this->assertTrue($form->has('similar_products'));
    }

    protected function setUp(): void
    {
        $this->associationTypeRepository = $this->createMock(RepositoryInterface::class);
        $this->transformer = $this->createMock(DataTransformerInterface::class);

        parent::setUp();
    }

    protected function getExtensions(): array
    {
        return [new PreloadedExtension(
            [new ProductAssociationsType($this->associationTypeRepository, $this->transformer)],
            [],
        )];
    }

    private function associationType(string $code): MockObject&ProductAssociationTypeInterface
    {
        $associationType = $this->createMock(ProductAssociationTypeInterface::class);
        $associationType->method('getCode')->willReturn($code);
        $associationType->method('getName')->willReturn($code);

        return $associationType;
    }
}
