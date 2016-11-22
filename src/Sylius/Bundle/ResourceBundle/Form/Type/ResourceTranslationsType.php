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
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\FormBuilderInterface;

/**
 * @author Anna Walasek <anna.walasek@lakion.com>
 */
class ResourceTranslationsType extends AbstractType
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
            if ($this->localeProvider->getDefaultLocaleCode() === $locale) {
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
        return CollectionType::class;
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'sylius_translations';
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'sylius_translations';
    }
}
