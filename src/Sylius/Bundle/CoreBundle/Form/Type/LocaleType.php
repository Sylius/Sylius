<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\CoreBundle\Form\Type;

use Sylius\Bundle\LocaleBundle\Form\Type\LocaleType as BaseLocaleType;
use Sylius\Component\Locale\Model\LocaleInterface;
use Sylius\Component\Resource\Repository\RepositoryInterface;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Intl\Intl;

/**
 * @author Kamil Kokot <kamil.kokot@lakion.com>
 */
class LocaleType extends BaseLocaleType
{
    /**
     * @var string
     */
    private $baseLocale;

    /**
     * @var RepositoryInterface
     */
    private $localeRepository;

    /**
     * {@inheritdoc}
     *
     * @param string $baseLocale
     * @param RepositoryInterface $localeRepository
     */
    public function __construct(
        $dataClass,
        array $validationGroups = [],
        $baseLocale,
        RepositoryInterface $localeRepository
    ) {
        parent::__construct($dataClass, $validationGroups);

        $this->baseLocale = $baseLocale;
        $this->localeRepository = $localeRepository;
    }

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        parent::buildForm($builder, $options);

        $builder->addEventListener(FormEvents::PRE_SET_DATA, function (FormEvent $event) {
            // Adding dynamically created code field
            $nameOptions = [
                'label' => 'sylius.form.locale.name',
            ];

            $locale = $event->getData();

            if ($locale instanceof LocaleInterface && null !== $locale->getCode()) {
                $nameOptions['disabled'] = true;
                $nameOptions['choices'] = [
                    $locale->getCode() => $this->getLocaleName($locale->getCode())
                ];
            } else {
                $nameOptions['choices'] = $this->getAvailableLocales();
            }

            $form = $event->getForm();
            $form->add('code', 'locale', $nameOptions);

            if ($this->baseLocale !== $locale->getCode()) {
                return;
            }

            $form->add('enabled', 'checkbox', [
                'label' => 'sylius.form.locale.enabled',
                'disabled' => true,
            ]);
        });
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'sylius_locale';
    }

    /**
     * @param $code
     *
     * @return null|string
     */
    private function getLocaleName($code)
    {
        return Intl::getLocaleBundle()->getLocaleName($code);
    }

    /**
     * @return array
     */
    private function getAvailableLocales()
    {
        $availableLocales = Intl::getLocaleBundle()->getLocaleNames();

        /** @var LocaleInterface[] $definedLocales */
        $definedLocales = $this->localeRepository->findAll();

        foreach ($definedLocales as $locale) {
            unset($availableLocales[$locale->getCode()]);
        }

        return $availableLocales;
    }
}
