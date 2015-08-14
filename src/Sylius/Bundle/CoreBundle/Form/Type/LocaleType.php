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

use Sylius\Bundle\ResourceBundle\Form\Type\AbstractResourceType;
use Sylius\Component\Locale\Model\LocaleInterface;
use Sylius\Component\Resource\Repository\RepositoryInterface;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Intl\Intl;

/**
 * @author Kamil Kokot <kamil.kokot@lakion.com>
 */
class LocaleType extends AbstractResourceType
{
    /**
     * @var RepositoryInterface
     */
    private $localeRepository;

    /**
     * {@inheritdoc}
     *
     * @param RepositoryInterface $localeRepository
     */
    public function __construct($dataClass, array $validationGroups = array(), RepositoryInterface $localeRepository)
    {
        parent::__construct($dataClass, $validationGroups);

        $this->localeRepository = $localeRepository;
    }

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $self = $this;
        $builder->addEventListener(
            FormEvents::PRE_SET_DATA,
            function (FormEvent $event) use ($self) {
                // Adding dynamically created code field
                $nameOptions = array(
                    'label' => 'sylius.form.locale.name',
                );

                $locale = $event->getData();
                if ($locale instanceof LocaleInterface && null !== $locale->getCode()) {
                    $nameOptions['disabled'] = true;
                } else {
                    $nameOptions['choices'] = $self->getAvailableLocales();
                }

                $form = $event->getForm();
                $form->add('code', 'locale', $nameOptions);
            }
        );
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'sylius_locale';
    }

    /**
     * Should be private, used public to support PHP 5.3
     *
     * @internal
     *
     * @return array
     */
    public function getAvailableLocales()
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
