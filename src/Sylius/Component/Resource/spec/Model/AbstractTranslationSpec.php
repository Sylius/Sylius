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

namespace spec\Sylius\Component\Resource\Model;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Sylius\Component\Resource\Model\AbstractTranslation;
use Sylius\Component\Resource\Model\TranslatableInterface;
use Sylius\Component\Resource\Model\TranslationInterface;

final class AbstractTranslationSpec extends ObjectBehavior
{
    function let(): void
    {
        $this->beAnInstanceOf('spec\Sylius\Component\Resource\Model\ConcreteTranslation');
    }

    function it_is_a_translation(): void
    {
        $this->shouldImplement(TranslationInterface::class);
    }

    function its_translatable_is_mutable(TranslatableInterface $translatable): void
    {
        $this->setTranslatable($translatable);
        $this->getTranslatable()->shouldReturn($translatable);
    }

    function its_detaches_from_its_translatable_correctly(
        TranslatableInterface $translatable1,
        TranslatableInterface $translatable2
    ): void {
        $translatable1->addTranslation(Argument::type(AbstractTranslation::class));
        $this->setTranslatable($translatable1);

        $translatable1->removeTranslation(Argument::type(AbstractTranslation::class));
        $translatable2->addTranslation(Argument::type(AbstractTranslation::class));
        $this->setTranslatable($translatable2);
    }

    function its_locale_is_mutable(): void
    {
        $this->setLocale('en');
        $this->getLocale()->shouldReturn('en');
    }
}

class ConcreteTranslation extends AbstractTranslation
{
}
