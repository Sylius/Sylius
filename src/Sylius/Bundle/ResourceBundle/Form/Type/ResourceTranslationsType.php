<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\ResourceBundle\Form\Type;

use Sylius\Component\Resource\Provider\AvailableLocalesProviderInterface;
use Sylius\Bundle\ResourceBundle\Form\EventSubscriber\ResourceTranslationsSubscriber;
use Sylius\Component\Resource\Provider\LocaleProviderInterface;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * @author Anna Walasek <anna.walasek@lakion.com>
 */
class ResourceTranslationsType extends AbstractType
{
    /**
     * @var LocaleProviderInterface
     */
    protected $localeProvider;

    /**
     * @var AvailableLocalesProviderInterface
     */
    protected $availableLocalesProvider;

    /**
     * @param LocaleProviderInterface $localeProvider
     * @param AvailableLocalesProviderInterface $availableLocalesProvider
     */
    public function __construct(LocaleProviderInterface $localeProvider, AvailableLocalesProviderInterface $availableLocalesProvider)
    {
        $this->localeProvider = $localeProvider;
        $this->availableLocalesProvider = $availableLocalesProvider;
    }

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $locales = $this->availableLocalesProvider->getAvailableLocales();
        $localesWithRequirement = [];

        foreach ($locales as $locale) {
            $localesWithRequirement[$locale] = false;
            if ($this->localeProvider->getDefaultLocale() === $locale) {
                $localesWithRequirement[$locale] = true;
                $localesWithRequirement = array_reverse($localesWithRequirement, true);
            }
        }

        $builder->addEventSubscriber(new ResourceTranslationsSubscriber($localesWithRequirement));
    }

    /**
     * {@inheritdoc}
     */
    public function getParent()
    {
        return 'collection';
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'sylius_translations';
    }
}
