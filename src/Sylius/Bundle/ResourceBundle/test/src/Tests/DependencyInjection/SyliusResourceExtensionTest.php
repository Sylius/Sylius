<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\ResourceBundle\Tests\DependencyInjection;

use AppBundle\Entity\Book;
use AppBundle\Form\Type\BookType;
use Matthias\SymfonyDependencyInjectionTest\PhpUnit\AbstractExtensionTestCase;
use Sylius\Bundle\ResourceBundle\DependencyInjection\SyliusResourceExtension;
use Sylius\Bundle\ResourceBundle\Form\Type\ResourceChoiceType;
use Sylius\Component\Resource\Factory\Factory;

/**
 * @author Anna Walasek <anna.walasek@lakion.com>
 */
class SyliusResourceExtensionTest extends AbstractExtensionTestCase
{
    /**
     * @test
     */
    public function it_registers_services_and_parameters_for_resources()
    {
        $this->load(
            [
                'resources' => [
                    'app.book' => [
                        'classes' => [
                            'model' => Book::class,
                            'form' => [
                                'default' => BookType::class,
                                'choice' => ResourceChoiceType::class,
                            ]
                        ]
                    ]
                ]
            ]
        );

        $this->assertContainerBuilderHasService('app.factory.book');
        $this->assertContainerBuilderHasService('app.form.type.book');
        $this->assertContainerBuilderHasService('app.form.type.book_choice');
        $this->assertContainerBuilderHasService('app.repository.book');
        $this->assertContainerBuilderHasService('app.controller.book');
        $this->assertContainerBuilderHasService('app.manager.book');

        $this->assertContainerBuilderHasParameter('app.model.book.class', Book::class);
    }

    /**
     * {@inheritdoc}
     */
    protected function getContainerExtensions()
    {
        return [
            new SyliusResourceExtension(),
        ];
    }
}
