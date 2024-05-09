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

namespace Sylius\Bundle\AdminBundle\Tests\EventListener;

use PHPUnit\Framework\TestCase;
use Sylius\Bundle\AdminBundle\EventListener\TwigComponentRenderListener;
use Symfony\UX\TwigComponent\AnonymousComponent;
use Symfony\UX\TwigComponent\ComponentAttributes;
use Symfony\UX\TwigComponent\ComponentMetadata;
use Symfony\UX\TwigComponent\Event\PreRenderEvent;
use Symfony\UX\TwigComponent\MountedComponent;

final class TwigComponentRenderListenerTest extends TestCase
{
    /** @test */
    public function it_replaces_camel_cased_hookable_metadata_variable_with_snake_cased_one(): void
    {
        $listener = new TwigComponentRenderListener();

        $event = new PreRenderEvent(
            new MountedComponent('sylius_admin:example', new AnonymousComponent(), new ComponentAttributes([])),
            new ComponentMetadata(['key' => 'sylius_admin:example', 'template' => 'example.html.twig']),
            ['hookableMetadata' => 'a_hookable_metadata_object_here']
        );

        $listener->onPreRender($event);

        $this->assertArrayHasKey('hookable_metadata', $event->getVariables());
        $this->assertArrayNotHasKey('hookableMetadata', $event->getVariables());
        $this->assertSame('a_hookable_metadata_object_here', $event->getVariables()['hookable_metadata']);
    }

    /** @test */
    public function it_does_nothing_when_the_component_name_is_not_starting_with_sylius_admin(): void
    {
        $listener = new TwigComponentRenderListener();

        $event = new PreRenderEvent(
            new MountedComponent('example', new AnonymousComponent(), new ComponentAttributes([])),
            new ComponentMetadata(['key' => 'example', 'template' => 'example.html.twig']),
            ['hookableMetadata' => 'a_hookable_metadata_object_here']
        );

        $listener->onPreRender($event);

        $this->assertArrayNotHasKey('hookable_metadata', $event->getVariables());
        $this->assertArrayHasKey('hookableMetadata', $event->getVariables());
    }

    /** @test */
    public function it_does_nothing_when_the_component_is_not_an_anonymous_component(): void
    {
        $listener = new TwigComponentRenderListener();

        $event = new PreRenderEvent(
            new MountedComponent('sylius_admin:example', new \stdClass(), new ComponentAttributes([])),
            new ComponentMetadata(['key' => 'sylius_admin:example', 'template' => 'example.html.twig']),
            ['hookableMetadata' => 'a_hookable_metadata_object_here']
        );

        $listener->onPreRender($event);

        $this->assertArrayNotHasKey('hookable_metadata', $event->getVariables());
        $this->assertArrayHasKey('hookableMetadata', $event->getVariables());
    }

    /** @test */
    public function it_does_nothing_when_there_is_no_camel_cased_hookable_metadata_variable(): void
    {
        $listener = new TwigComponentRenderListener();

        $event = new PreRenderEvent(
            new MountedComponent('sylius_admin:example', new AnonymousComponent(), new ComponentAttributes([])),
            new ComponentMetadata(['key' => 'sylius_admin:example', 'template' => 'example.html.twig']),
            ['something' => 'some_value']
        );

        $listener->onPreRender($event);

        $this->assertArrayHasKey('something', $event->getVariables());
        $this->assertArrayNotHasKey('hookableMetadata', $event->getVariables());
        $this->assertArrayNotHasKey('hookable_metadata', $event->getVariables());
    }
}
