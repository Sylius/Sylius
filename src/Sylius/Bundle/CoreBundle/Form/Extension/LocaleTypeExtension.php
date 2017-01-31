<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\CoreBundle\Form\Extension;

use Sylius\Bundle\LocaleBundle\Form\Type\LocaleType;
use Sylius\Component\Locale\Model\LocaleInterface;
use Sylius\Component\Resource\Repository\RepositoryInterface;
use Symfony\Component\Form\AbstractTypeExtension;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Intl\Intl;

/**
 * @author Kamil Kokot <kamil.kokot@lakion.com>
 */
final class LocaleTypeExtension extends AbstractTypeExtension
{
    /**
     * @var RepositoryInterface
     */
    private $localeRepository;

    /**
     * @param RepositoryInterface $localeRepository
     */
    public function __construct(RepositoryInterface $localeRepository)
    {
        $this->localeRepository = $localeRepository;
    }

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
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

    /**
     * {@inheritdoc}
     */
    public function getExtendedType()
    {
        return LocaleType::class;
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
