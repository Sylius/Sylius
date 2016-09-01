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

use Sylius\Bundle\ResourceBundle\Form\EventSubscriber\ResourceTranslationsSubscriber;
use Sylius\Component\Locale\Provider\LocaleProviderInterface;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * @author Anna Walasek <anna.walasek@lakion.com>
 */
final class ResourceTranslationsType extends AbstractType
{
    /**
     * @var LocaleProviderInterface
     */
    private $localeProvider;

    /**
     * @param LocaleProviderInterface $localeProvider
     */
    public function __construct(LocaleProviderInterface $localeProvider)
    {
        $this->localeProvider = $localeProvider;
    }

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $locales = $this->localeProvider->getDefinedLocalesCodes();
        $localesWithRequirement = [];

        foreach ($locales as $locale) {
            $localesWithRequirement[$locale] = false;
            if ($this->isLocaleRequired($locale, $options)) {
                $localesWithRequirement[$locale] = true;
                $localesWithRequirement = array_reverse($localesWithRequirement, true);
            }
        }

        $builder->addEventSubscriber(new ResourceTranslationsSubscriber($localesWithRequirement));
    }

    /**
     * @param string $locale
     * @param array $options
     *
     * @return bool
     */
    private function isLocaleRequired($locale, array $options = [])
    {
        if (isset($options['required_locales'])) {
            return in_array($locale, $options['required_locales']);
        }

        return $this->localeProvider->getDefaultLocaleCode() === $locale;
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver
            ->setDefined('required_locales')
            ->setAllowedTypes('required_locales', ['array'])
        ;
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
