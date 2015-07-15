<?php

namespace spec\Sylius\Component\Translation\Model;

use PhpSpec\ObjectBehavior;
use Sylius\Component\Translation\Model\TranslationInterface;
use Sylius\Component\Translation\Model\AbstractTranslatable;
use Sylius\Component\Translation\Model\AbstractTranslation;

class AbstractTranslatableSpec extends ObjectBehavior
{
    public function let()
    {
        $this->beAnInstanceOf('spec\Sylius\Component\Translation\Model\ConcreteTranslatable');
    }

    public function it_is_translatable()
    {
        $this->shouldImplement('Sylius\Component\Translation\Model\TranslatableInterface');
    }

    public function it_initializes_translattion_collection_by_default()
    {
        $this->getTranslations()->shouldHaveType('Doctrine\Common\Collections\Collection');
    }

    public function it_adds_translation(TranslationInterface $translation)
    {
        $translation->getLocale()->willReturn('en');
        $translation->setTranslatable($this)->shouldBeCalled();

        $this->addTranslation($translation)->shouldReturn($this);
        $this->hasTranslation($translation)->shouldReturn(true);
    }

    public function it_removes_translation(TranslationInterface $translation)
    {
        $this->addTranslation($translation);
        $this->removeTranslation($translation)->shouldReturn($this);

        $this->hasTranslation($translation)->shouldReturn(false);
    }

    public function its_current_locale_is_mutable()
    {
        $this->setCurrentLocale('en_US')->shouldReturn($this);
        $this->getCurrentLocale()->shouldReturn('en_US');
    }

    public function its_fallback_locale_is_mutable()
    {
        $this->setFallbackLocale('en_US');
        $this->getFallbackLocale()->shouldReturn('en_US');
    }

    public function it_throws_exception_if_no_locale_has_been_set()
    {
        $this->shouldThrow('\RuntimeException')->duringTranslate();
    }

    public function it_translates_properly(TranslationInterface $translation)
    {
        $translation->getLocale()->willReturn('en_US');
        $translation->setTranslatable($this)->shouldBeCalled();

        $this->addTranslation($translation);
        $this->setCurrentLocale('en_US');
        $this->setFallbackLocale('en_US');

        $this->translate()->shouldReturn($translation);
    }

    public function it_creates_new_empty_translation_properly()
    {
        $this->setCurrentLocale('en_US');
        $this->setFallbackLocale('en_US');
        $this->translate()->shouldHaveType('spec\Sylius\Component\Translation\Model\ConcreteTranslatableTranslation');
    }

    public function it_clones_new_translation_properly(TranslationInterface $translation)
    {
        $translation->getLocale()->willReturn('en');
        $translation->setTranslatable($this)->shouldBeCalled();
        $translation->acmeProperty = 'acmeProp';

        $this->addTranslation($translation);
        $this->setCurrentLocale('en');

        $translation = $this->translate();
        $translation->shouldImplement('Sylius\Component\Translation\Model\TranslationInterface');
        $translation->acmeProperty->shouldBe('acmeProp');
    }
}

class ConcreteTranslatable extends AbstractTranslatable
{
    public static function getTranslationClass()
    {
        return  'spec\Sylius\Component\Translation\Model\ConcreteTranslatableTranslation';
    }
}

class ConcreteTranslatableTranslation extends AbstractTranslation
{
}
