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

namespace Sylius\Bundle\CoreBundle\Form\Extension;

use Sylius\Bundle\LocaleBundle\Form\Type\LocaleType;
use Sylius\Component\Locale\Model\LocaleInterface;
use Sylius\Component\Resource\Repository\RepositoryInterface;
use Symfony\Component\Form\AbstractTypeExtension;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Intl\Locales;

final class LocaleTypeExtension extends AbstractTypeExtension
{
    public function __construct(private RepositoryInterface $localeRepository)
    {
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder->addEventListener(FormEvents::PRE_SET_DATA, function (FormEvent $event) {
            $options = [
                'label' => 'sylius.form.locale.name',
                'choice_loader' => null,
            ];

            $locale = $event->getData();
            if ($locale instanceof LocaleInterface && null !== $locale->getCode()) {
                $options['disabled'] = true;

                $options['choices'] = [$this->getLocaleName($locale->getCode()) => $locale->getCode()];
            } else {
                $options['choices'] = array_flip($this->getAvailableLocales());
            }

            $form = $event->getForm();
            $form->add('code', \Symfony\Component\Form\Extension\Core\Type\LocaleType::class, $options);
        });
    }

    public static function getExtendedTypes(): iterable
    {
        return [LocaleType::class];
    }

    private function getLocaleName(string $code): string
    {
        return Locales::getName($code);
    }

    /** @return string[] */
    private function getAvailableLocales(): array
    {
        $availableLocales = Locales::getNames();

        /** @var LocaleInterface[] $definedLocales */
        $definedLocales = $this->localeRepository->findAll();

        foreach ($definedLocales as $locale) {
            unset($availableLocales[$locale->getCode()]);
        }

        return $availableLocales;
    }
}
