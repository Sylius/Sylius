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

use Prophecy\Prophecy\ObjectProphecy;
use Sylius\Bundle\ResourceBundle\Form\Type\ResourceTranslationsType;
use Sylius\Component\Resource\Translation\Provider\TranslationLocaleProviderInterface;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\PreloadedExtension;
use Symfony\Component\Form\Test\TypeTestCase;

/**
 * @author Kamil Kokot <kamil@kokot.me>
 */
final class ResourceTranslationsTypeTest extends TypeTestCase
{
    /**
     * {@inheritdoc}
     */
    protected function getExtensions(): array
    {
        /** @var TranslationLocaleProviderInterface|ObjectProphecy $translationLocaleProvider */
        $translationLocaleProvider = $this->prophesize(TranslationLocaleProviderInterface::class);
        $translationLocaleProvider->getDefaultLocaleCode()->willReturn('en_US');
        $translationLocaleProvider->getDefinedLocalesCodes()->willReturn(['en_US', 'pl_PL']);

        $resourceTranslationsType = new ResourceTranslationsType($translationLocaleProvider->reveal());

        return [
            new PreloadedExtension([$resourceTranslationsType], []),
        ];
    }

    /**
     * @test
     */
    public function it_respects_entry_options(): void
    {
        $form = $this->factory->create(
            ResourceTranslationsType::class,
            null,
            ['entry_type' => TextType::class, 'entry_options' => ['empty_data' => 'Default']]
        );

        // Testing child type because of `$event->getForm()->getParent()->getData()` inside ResourceTranslationsType::submit()
        $englishTranslationForm = $form->get('en_US');
        $englishTranslationForm->submit(null);

        $this->assertEquals('Default', $englishTranslationForm->getData());
    }
}
