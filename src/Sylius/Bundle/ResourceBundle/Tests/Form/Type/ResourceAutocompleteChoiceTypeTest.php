<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Sylius\Bundle\ResourceBundle\Tests\Form\Type;

use Doctrine\Common\Collections\ArrayCollection;
use Prophecy\Prophecy\ObjectProphecy;
use Sylius\Bundle\ResourceBundle\Form\Type\ResourceAutocompleteChoiceType;
use Sylius\Component\Registry\ServiceRegistryInterface;
use Sylius\Component\Resource\Model\ResourceInterface;
use Sylius\Component\Resource\Repository\RepositoryInterface;
use Symfony\Component\Form\PreloadedExtension;
use Symfony\Component\Form\Test\TypeTestCase;
use Symfony\Component\OptionsResolver\Exception\InvalidOptionsException;
use Symfony\Component\OptionsResolver\Exception\MissingOptionsException;

final class ResourceAutocompleteChoiceTypeTest extends TypeTestCase
{
    /**
     * @var ObjectProphecy|ServiceRegistryInterface
     */
    private $resourceRepositoryRegistry;

    protected function setUp(): void
    {
        $this->resourceRepositoryRegistry = $this->prophesize(ServiceRegistryInterface::class);

        parent::setUp();
    }

    protected function getExtensions(): array
    {
        $resourceAutoCompleteType = new ResourceAutocompleteChoiceType($this->resourceRepositoryRegistry->reveal());

        return [
            new PreloadedExtension([$resourceAutoCompleteType], []),
        ];
    }

    /**
     * @test
     */
    public function it_returns_resource_from_its_code(): void
    {
        /** @var ObjectProphecy|RepositoryInterface $resourceRepository */
        $resourceRepository = $this->prophesize(RepositoryInterface::class);
        $resource = $this->prophesize(ResourceInterface::class);

        $this->resourceRepositoryRegistry->get('sylius.resource')->willReturn($resourceRepository);
        $resourceRepository->findOneBy(['code' => 'mug'])->willReturn($resource);

        $form = $this->factory->create(
            ResourceAutocompleteChoiceType::class,
            null,
            ['resource' => 'sylius.resource', 'choice_name' => 'name', 'choice_value' => 'code']
        );

        $form->submit('mug');

        $this->assertEquals($resource->reveal(), $form->getData());
    }

    /**
     * @test
     */
    public function it_returns_resource_from_its_id(): void
    {
        /** @var ObjectProphecy|RepositoryInterface $resourceRepository */
        $resourceRepository = $this->prophesize(RepositoryInterface::class);
        $resource = $this->prophesize(ResourceInterface::class);

        $this->resourceRepositoryRegistry->get('sylius.resource')->willReturn($resourceRepository);
        $resourceRepository->findOneBy(['id' => '1'])->willReturn($resource);

        $form = $this->factory->create(
            ResourceAutocompleteChoiceType::class,
            null,
            ['resource' => 'sylius.resource', 'choice_name' => 'name', 'choice_value' => 'id']
        );

        $form->submit('1');

        $this->assertEquals($resource->reveal(), $form->getData());
    }

    /**
     * @test
     */
    public function it_returns_different_resource_from_its_identifier(): void
    {
        /** @var ObjectProphecy|RepositoryInterface $resourceRepository */
        $resourceRepository = $this->prophesize(RepositoryInterface::class);
        $resource = $this->prophesize(ResourceInterface::class);

        $this->resourceRepositoryRegistry->get('sylius.zone')->willReturn($resourceRepository);
        $resourceRepository->findOneBy(['code' => 'eu'])->willReturn($resource);

        $form = $this->factory->create(
            ResourceAutocompleteChoiceType::class,
            null,
            ['resource' => 'sylius.zone', 'choice_name' => 'name', 'choice_value' => 'code']
        );

        $form->submit('eu');

        $this->assertEquals($resource->reveal(), $form->getData());
    }

    /**
     * @test
     */
    public function it_has_identifier_as_view_value(): void
    {
        /** @var ObjectProphecy|RepositoryInterface $resourceRepository */
        $resourceRepository = $this->prophesize(RepositoryInterface::class);
        $resource = $this->prophesize(ResourceInterface::class);

        $this->resourceRepositoryRegistry->get('sylius.zone')->willReturn($resourceRepository);
        $resourceRepository->findOneBy(['code' => 'eu'])->willReturn($resource);

        $form = $this->factory->create(
            ResourceAutocompleteChoiceType::class,
            null,
            ['resource' => 'sylius.zone', 'choice_name' => 'name', 'choice_value' => 'code']
        );

        $form->submit('eu');

        $this->assertEquals('eu', $form->getViewData());
    }

    /**
     * @test
     */
    public function it_has_different_view_based_on_passed_configuration(): void
    {
        /** @var ObjectProphecy|RepositoryInterface $resourceRepository */
        $resourceRepository = $this->prophesize(RepositoryInterface::class);
        $resource = $this->prophesize(ResourceInterface::class);

        $this->resourceRepositoryRegistry->get('sylius.zone')->willReturn($resourceRepository);
        $resourceRepository->findOneBy(['code' => 'eu'])->willReturn($resource);

        $form = $this->factory->create(
            ResourceAutocompleteChoiceType::class,
            null,
            ['resource' => 'sylius.zone', 'choice_name' => 'name', 'choice_value' => 'code']
        );

        $this->assertArraySubset(
            ['choice_name' => 'name', 'choice_value' => 'code', 'multiple' => false, 'placeholder' => ''],
            $form->createView()->vars
        );
    }

    /**
     * @test
     */
    public function it_returns_collection_of_resources_from_identifiers(): void
    {
        /** @var ObjectProphecy|RepositoryInterface $resourceRepository */
        $resourceRepository = $this->prophesize(RepositoryInterface::class);
        $mug = $this->prophesize(ResourceInterface::class);
        $book = $this->prophesize(ResourceInterface::class);
        $sticker = $this->prophesize(ResourceInterface::class);

        $this->resourceRepositoryRegistry->get('sylius.resource')->willReturn($resourceRepository);
        $resourceRepository->findOneBy(['code' => 'mug'])->willReturn($mug);
        $resourceRepository->findOneBy(['code' => 'book'])->willReturn($book);
        $resourceRepository->findOneBy(['code' => 'sticker'])->willReturn($sticker);

        $form = $this->factory->create(
            ResourceAutocompleteChoiceType::class,
            new ArrayCollection(),
            ['resource' => 'sylius.resource', 'choice_name' => 'name', 'choice_value' => 'code', 'multiple' => true]
        );

        $form->submit('mug,book,sticker');

        $this->assertEquals(
            new ArrayCollection([$mug->reveal(), $book->reveal(), $sticker->reveal()]),
            $form->getData()
        );
    }

    /**
     * @test
     */
    public function its_resource_option_should_be_string(): void
    {
        $this->expectException(InvalidOptionsException::class);

        $this->factory->create(
            ResourceAutocompleteChoiceType::class,
            new ArrayCollection(),
            ['resource' => 1, 'choice_name' => 'name', 'choice_value' => 'code', 'multiple' => true]
        );
    }

    /**
     * @test
     */
    public function its_choice_name_option_should_be_string(): void
    {
        $this->expectException(InvalidOptionsException::class);

        $this->factory->create(
            ResourceAutocompleteChoiceType::class,
            new ArrayCollection(),
            ['resource' => 1, 'choice_name' => 1, 'choice_value' => 'code', 'multiple' => true]
        );
    }

    /**
     * @test
     */
    public function its_choice_value_option_should_be_string(): void
    {
        $this->expectException(InvalidOptionsException::class);

        $this->factory->create(
            ResourceAutocompleteChoiceType::class,
            new ArrayCollection(),
            ['resource' => 'sylius.resource', 'choice_name' => 'name', 'choice_value' => 1, 'multiple' => true]
        );
    }

    /**
     * @test
     */
    public function its_multiple_option_should_be_boolean(): void
    {
        $this->expectException(InvalidOptionsException::class);

        $this->factory->create(
            ResourceAutocompleteChoiceType::class,
            new ArrayCollection(),
            ['resource' => 'sylius.resource', 'choice_name' => 'name', 'choice_value' => 'code', 'multiple' => 'yes']
        );
    }

    /**
     * @test
     */
    public function its_placeholder_option_should_be_string(): void
    {
        $this->expectException(InvalidOptionsException::class);

        $this->factory->create(
            ResourceAutocompleteChoiceType::class,
            new ArrayCollection(),
            ['resource' => 'sylius.resource', 'choice_name' => 'name', 'choice_value' => 'code', 'placeholder' => 1]
        );
    }

    /**
     * @test
     */
    public function it_cannot_be_created_without_resource_option(): void
    {
        $this->expectException(MissingOptionsException::class);

        $this->factory->create(
            ResourceAutocompleteChoiceType::class,
            new ArrayCollection(),
            ['choice_name' => 'name', 'choice_value' => 'code']
        );
    }

    /**
     * @test
     */
    public function it_cannot_be_created_without_choice_name_option(): void
    {
        $this->expectException(MissingOptionsException::class);

        $this->factory->create(
            ResourceAutocompleteChoiceType::class,
            new ArrayCollection(),
            ['resource' => 'sylius.resource', 'choice_value' => 'code']
        );
    }

    /**
     * @test
     */
    public function it_cannot_be_created_without_choice_value_option(): void
    {
        $this->expectException(MissingOptionsException::class);

        $this->factory->create(
            ResourceAutocompleteChoiceType::class,
            new ArrayCollection(),
            ['resource' => 'sylius.resource', 'choice_name' => 'name']
        );
    }
}
