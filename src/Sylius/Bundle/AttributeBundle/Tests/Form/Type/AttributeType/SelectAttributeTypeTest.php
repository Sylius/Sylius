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

namespace Sylius\Bundle\AttributeBundle\Tests\Form\Type\AttributeType;

use PHPUnit\Framework\Assert;
use Prophecy\Prophecy\ObjectProphecy;
use Sylius\Bundle\AttributeBundle\Form\Type\AttributeType\SelectAttributeType;
use Sylius\Component\Resource\Translation\Provider\TranslationLocaleProviderInterface;
use Symfony\Component\Form\ChoiceList\View\ChoiceView;
use Symfony\Component\Form\PreloadedExtension;
use Symfony\Component\Form\Test\TypeTestCase;

final class SelectAttributeTypeTest extends TypeTestCase
{
    private ObjectProphecy $translationProvider;

    /**
     * @test
     */
    public function it_return_all_choices(): void
    {
        $this->assertChoicesLabels(['value 1'], [
            'configuration' => [
                'multiple' => false,
                'min' => null,
                'max' => null,
                'choices' => ['val1' => ['en_GB' => 'value 1'], 'val2' => ['fr_FR' => 'valeur 2']],
            ],
        ]);
    }

    protected function setUp(): void
    {
        $this->translationProvider = $this->prophesize(TranslationLocaleProviderInterface::class);
        $this->translationProvider->getDefaultLocaleCode()->willReturn('en_GB');

        parent::setUp();
    }

    protected function getExtensions(): array
    {
        $type = new SelectAttributeType($this->translationProvider->reveal());

        return [
            new PreloadedExtension([$type], []),
        ];
    }

    private function assertChoicesLabels(array $expectedLabels, array $formConfiguration = []): void
    {
        $form = $this->factory->create(
            SelectAttributeType::class,
            null,
            $formConfiguration,
        );
        $view = $form->createView();

        Assert::assertSame($expectedLabels, array_map(fn (ChoiceView $choiceView): string => (string) $choiceView->label, $view->vars['choices']));
    }
}
