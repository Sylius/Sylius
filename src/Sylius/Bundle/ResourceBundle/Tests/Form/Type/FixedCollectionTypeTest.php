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

use Sylius\Bundle\ResourceBundle\Form\Type\FixedCollectionType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Test\TypeTestCase;

/**
 * @author Kamil Kokot <kamil@kokot.me>
 */
final class FixedCollectionTypeTest extends TypeTestCase
{
    /**
     * @test
     */
    public function it_builds_fixed_collection(): void
    {
        $form = $this->factory->create(FixedCollectionType::class, null, [
            'entries' => ['first_name', 'last_name'],
            'entry_type' => TextType::class,
            'entry_name' => function (string $entry): string {
                return strtoupper($entry);
            },
        ]);

        $form->submit(['FIRST_NAME' => 'Elon', 'LAST_NAME' => 'Tusk']);

        $this->assertEquals(['FIRST_NAME' => 'Elon', 'LAST_NAME' => 'Tusk'], $form->getData());
    }

    /**
     * @test
     */
    public function it_builds_fixed_collection_using_callable_to_resolve_entry_type(): void
    {
        $form = $this->factory->create(FixedCollectionType::class, null, [
            'entries' => ['first_name', 'last_name'],
            'entry_type' => function (string $entry): string {
                if (!in_array($entry, ['first_name', 'last_name'], true)) {
                    throw new \Exception();
                }

                return TextType::class;
            },
            'entry_name' => function (string $entry): string {
                return strtoupper($entry);
            },
        ]);

        $form->submit(['FIRST_NAME' => 'Elon', 'LAST_NAME' => 'Tusk']);

        $this->assertEquals(['FIRST_NAME' => 'Elon', 'LAST_NAME' => 'Tusk'], $form->getData());
    }

    /**
     * @test
     */
    public function it_builds_fixed_collection_using_array_to_resolve_entry_options(): void
    {
        $form = $this->factory->create(FixedCollectionType::class, null, [
            'entries' => ['first_name', 'last_name'],
            'entry_type' => TextType::class,
            'entry_name' => function (string $entry): string {
                return strtoupper($entry);
            },
            'entry_options' => [
                'empty_data' => 'Tusk',
            ],
        ]);

        $form->submit(['FIRST_NAME' => 'Elon']);

        $this->assertEquals(['FIRST_NAME' => 'Elon', 'LAST_NAME' => 'Tusk'], $form->getData());
    }

    /**
     * @test
     */
    public function it_builds_fixed_collection_using_callable_to_resolve_entry_options(): void
    {
        $form = $this->factory->create(FixedCollectionType::class, null, [
            'entries' => ['first_name', 'last_name'],
            'entry_type' => TextType::class,
            'entry_name' => function (string $entry): string {
                return strtoupper($entry);
            },
            'entry_options' => function (string $entry): array {
                $defaults = [
                    'first_name' => 'Elon',
                    'last_name' => 'Tusk',
                ];

                return ['empty_data' => $defaults[$entry]];
            },
        ]);

        $form->submit([]);

        $this->assertEquals(['FIRST_NAME' => 'Elon', 'LAST_NAME' => 'Tusk'], $form->getData());
    }
}
