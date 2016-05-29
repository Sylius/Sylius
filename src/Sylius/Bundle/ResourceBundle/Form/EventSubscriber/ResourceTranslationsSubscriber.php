<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\ResourceBundle\Form\EventSubscriber;

use Sylius\Component\Resource\Model\NullDetectableInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;

/**
 * @author Anna Walasek <anna.walasek@lakion.com>
 */
class ResourceTranslationsSubscriber implements EventSubscriberInterface
{
    /**
     * @var array
     */
    private $locales;

    /**
     * @param array $locales
     */
    public function __construct(array $locales)
    {
        $this->locales = $locales;
    }

    /**
     * {@inheritdoc}
     */
    public static function getSubscribedEvents()
    {
        return [
            FormEvents::PRE_SET_DATA => 'preSetData',
            FormEvents::SUBMIT => 'submit',
        ];
    }

    /**
     * @param FormEvent $event
     */
    public function preSetData(FormEvent $event)
    {
        $form = $event->getForm();
        // NOTE: This needs changing from SF 2.7 => 2.8 - type => entry_type, options => entry_options
        $type = $form->getConfig()->getOption('type');
        $options = $form->getConfig()->getOption('options');

        foreach ($this->locales as $locale => $isRequired) {
            if (false === $form->has($locale)) {
                $form->add($locale, $type, array_merge($options, ['required' => $isRequired]));
            }
        }
    }

    /**
     * @param FormEvent $event
     */
    public function submit(FormEvent $event)
    {
        $data = $event->getData();
        $parent = $event->getForm()->getParent();
        $translatable = $parent->getData();

        foreach ($data as $locale => $translation) {
            if (null === $translation || ($translation instanceof NullDetectableInterface && $translation->isNull())) {
                unset($data[$locale]);
                continue;
            }
            $translation->setLocale($locale);
            $translation->setTranslatable($translatable);
        }
        $event->setData($data);
    }
}
