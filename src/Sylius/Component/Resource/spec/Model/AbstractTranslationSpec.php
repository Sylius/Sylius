<?php

namespace spec\Sylius\Component\Translation\Model;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Sylius\Component\Resource\Model\TranslatableInterface;

class AbstractTranslationSpec extends ObjectBehavior
{
    public function let()
    {
        $this->beAnInstanceOf('spec\Sylius\Component\Resource\Model\ConcreteTranslation');
    }

    public function it_is_a_translation()
    {
        $this->shouldImplement('Sylius\Component\Resource\Model\TranslationInterface');
    }

    public function its_translatable_is_mutabale(TranslatableInterface $translatable)
    {
        $this->setTranslatable($translatable)->shouldReturn($this);
        $this->getTranslatable()->shouldReturn($translatable);
    }

    public function its_detaches_from_its_translatable_correctly(
        TranslatableInterface $translatable1,
        TranslatableInterface $translatable2
    ) {
        $translatable1->addTranslation(Argument::type('Sylius\Component\Resource\Model\AbstractTranslation'));
        $this->setTranslatable($translatable1);

        $translatable1->removeTranslation(Argument::type('Sylius\Component\Resource\Model\AbstractTranslation'));
        $translatable2->addTranslation(Argument::type('Sylius\Component\Resource\Model\AbstractTranslation'));
        $this->setTranslatable($translatable2);
    }

    public function its_locale_is_mutable()
    {
        $this->setLocale('en')->shouldReturn($this);
        $this->getLocale()->shouldReturn('en');
    }
}

class ConcreteTranslation extends \Sylius\Component\Resource\Model\AbstractTranslation
{
}
