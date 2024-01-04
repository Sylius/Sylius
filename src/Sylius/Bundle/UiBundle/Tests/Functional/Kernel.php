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

namespace Sylius\Bundle\UiBundle\Tests\Functional;

use Sonata\BlockBundle\SonataBlockBundle;
use Sylius\Bundle\UiBundle\SyliusUiBundle;
use Sylius\Bundle\UiBundle\Tests\Functional\src\CustomContextProvider;
use Sylius\Bundle\UiBundle\Tests\Functional\src\SomeTwigComponent;
use Symfony\Bundle\FrameworkBundle\FrameworkBundle;
use Symfony\Bundle\FrameworkBundle\Kernel\MicroKernelTrait;
use Symfony\Bundle\SecurityBundle\SecurityBundle;
use Symfony\Bundle\TwigBundle\TwigBundle;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Kernel as HttpKernel;
use Symfony\UX\TwigComponent\TwigComponentBundle;
use Symfony\WebpackEncoreBundle\WebpackEncoreBundle;

final class Kernel extends HttpKernel
{
    use MicroKernelTrait;

    public function registerBundles(): array
    {
        return [
            new FrameworkBundle(),
            new SecurityBundle(),
            new TwigBundle(),
            new SonataBlockBundle(),
            new SyliusUiBundle(),
            new WebpackEncoreBundle(),
            new TwigComponentBundle(),
        ];
    }

    protected function build(ContainerBuilder $container)
    {
        $container->register(CustomContextProvider::class)->addTag('sylius.ui.template_event.context_provider');

        $container->register(SomeTwigComponent::class)->addTag('twig.component', ['template' => 'blocks/twigComponent/someTwigComponent.html.twig']);

        $container->loadFromExtension('framework', [
            'secret' => 'S0ME_SECRET',
        ]);

        $container->loadFromExtension(
            'sonata_block',
            ['blocks' => ['sonata.block.service.template' => ['settings' => ['context' => null]]]],
        );

        $container->loadFromExtension('sylius_ui', ['events' => [
            'first_event' => [
                'blocks' => [
                    'third' => ['template' => 'blocks/txt/third.txt.twig', 'priority' => -5],
                    'first' => ['template' => 'blocks/txt/first.txt.twig', 'priority' => 5],
                    'second' => 'blocks/txt/second.txt.twig',
                ],
            ],
            'second_event' => [
                'blocks' => [
                    'context' => 'blocks/txt/context.txt.twig',
                ],
            ],
            'event' => [
                'blocks' => [
                    'first' => ['template' => 'blocks/html/first.html.twig', 'priority' => 5],
                    'context' => ['template' => 'blocks/html/context.html.twig', 'priority' => -5],
                ],
            ],
            'context_template_block' => [
                'blocks' => [
                    'block' => [
                        'template' => 'blocks/contextTemplateBlock/block.txt.twig',
                        'context' => [
                            'option1' => 'foo',
                            'option2' => 'bar',
                        ],
                    ],
                ],
            ],
            'multiple_events_generic' => [
                'blocks' => [
                    'first' => [
                        'template' => 'blocks/multipleEvents/genericFirst.txt.twig',
                    ],
                    'second' => [
                        'template' => 'blocks/multipleEvents/genericSecond.txt.twig',
                        'context' => ['value' => 13],
                    ],
                ],
            ],
            'multiple_events_specific' => [
                'blocks' => [
                    'specific' => [
                        'template' => 'blocks/multipleEvents/specific.txt.twig',
                        'priority' => 3,
                    ],
                    'second' => [
                        'context' => ['value' => 42],
                        'priority' => 5,
                    ],
                ],
            ],
            'custom_context_provider' => [
                'blocks' => [
                    'block' => [
                        'template' => 'blocks/customContextProvider/block.txt.twig',
                        'context' => [
                            'option1' => 'foo',
                            'option2' => 'bar',
                        ],
                    ],
                ],
            ],
            'template_event' => [
                'blocks' => [
                    'block' => [
                        'component' => 'SomeTwigComponent',
                    ],
                ],
            ],
            'template_event_with_context' => [
                'blocks' => [
                    'block' => [
                        'component' => [
                            'name' => 'SomeTwigComponent',
                            'inputs' => [
                                'context' => 'expr:context',
                            ],
                        ],
                    ],
                ],
            ],
        ]]);

        $container->loadFromExtension('webpack_encore', [
            'output_path' => '%kernel.project_dir%/public/build',
        ]);
    }
}
