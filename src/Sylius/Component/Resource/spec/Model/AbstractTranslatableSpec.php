<?php

namespace spec\Sylius\Component\Resource\Model;

use PhpSpec\ObjectBehavior;
use Sylius\Component\Resource\Model\TranslationInterface;

class AbstractTranslatableSpec extends ObjectBehavior
{
    public function let()
    {
        $this->beAnInstanceOf('spec\Sylius\Component\Resource\Model\ConcreteTranslatable');
    }

    public function it_is_translatable()
    {
        $this->shouldImplement('Sylius\Component\Resource\Model\TranslatableInterface');
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
        $this->setCurrentLocale('en')->shouldReturn($this);
        $this->getCurrentLocale()->shouldReturn('en');
    }

    public function its_current_translation_is_mutable(TranslationInterface $translation)
    {
        $this->setCurrentTranslation($translation);
        $this->getCurrentTranslation()->shouldReturn($translation);
    }

    public function its_fallback_locale_is_mutable()
    {
        $this->setFallbackLocale('en');
        $this->getFallbackLocale()->shouldReturn('en');
    }

    public function it_throws_exception_if_no_locale_has_been_set()
    {
        $this->shouldThrow('\RuntimeException')->duringTranslate();
    }

    public function it_translates_properly(TranslationInterface $translation)
    {
        $translation->getLocale()->willReturn('en');
        $translation->setTranslatable($this)->shouldBeCalled();

        $this->addTranslation($translation);
        $this->setCurrentLocale('en');

        $this->translate()->shouldReturn($translation);
    }

    public function it_creates_new_empty_translation_properly()
    {
        $this->setCurrentLocale('en');
        $this->translate()->shouldHaveType('spec\Sylius\Component\Resource\Model\ConcreteTranslatableTranslation');
    }

    public function it_clones_new_translation_properly(TranslationInterface $translation)
    {
        $translation->getLocale()->willReturn('en');
        $translation->setTranslatable($this)->shouldBeCalled();
        $translation->acmeProperty = 'acmeProp';

        $this->addTranslation($translation);
        $this->setCurrentLocale('en');

        $translation = $this->translate();
        $translation->shouldImplement('Sylius\Component\Resource\Model\TranslationInterface');
        $translation->acmeProperty->shouldBe('acmeProp');
    }
}

class ConcreteTranslatable extends \Sylius\Component\Resource\Model\AbstractTranslatable
{
    protected function getTranslationEntityClass()
    {
        return  'spec\Sylius\Component\Resource\Model\ConcreteTranslatableTranslation';
    }
}

class ConcreteTranslatableTranslation extends \Sylius\Component\Resource\Model\AbstractTranslation
{
}
