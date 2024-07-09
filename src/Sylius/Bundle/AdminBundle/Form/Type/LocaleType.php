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

namespace Sylius\Bundle\AdminBundle\Form\Type;

use Sylius\Bundle\LocaleBundle\Form\Type\LocaleType as BaseLocaleType;
use Sylius\Component\Locale\Model\LocaleInterface;
use Sylius\Component\Resource\Repository\RepositoryInterface;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\LocaleType as SymfonyLocaleType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Intl\Locales;

final class LocaleType extends AbstractType
{
    /** @param RepositoryInterface<LocaleInterface> $localeRepository */
    public function __construct(private readonly RepositoryInterface $localeRepository)
    {
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder->addEventListener(FormEvents::PRE_SET_DATA, function (FormEvent $event): void {
            $options = [
                'label' => 'sylius.form.locale.name',
                'choice_loader' => null,
                'placeholder' => 'sylius.form.locale.select',
                'autocomplete' => true,
            ];

            $locale = $event->getData();
            if ($locale instanceof LocaleInterface && null !== $locale->getCode()) {
                $options['disabled'] = true;
                $options['choices'] = [ Locales::getName($locale->getCode()) => $locale->getCode()];
            } else {
                $options['choices'] = array_flip($this->getAvailableLocales());
            }

            $form = $event->getForm();
            $form->add('code', SymfonyLocaleType::class, $options);
        });
    }

    public function getBlockPrefix(): string
    {
        return 'sylius_admin_locale';
    }

    public function getParent(): string
    {
        return BaseLocaleType::class;
    }

    /** @return string[] */
    private function getAvailableLocales(): array
    {
        $availableLocales = Locales::getNames();

        $definedLocales = $this->localeRepository->findAll();

        foreach ($definedLocales as $locale) {
            unset($availableLocales[$locale->getCode()]);
        }

        return $availableLocales;
    }
}
