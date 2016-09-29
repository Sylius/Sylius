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

use Sylius\Component\Locale\Model\LocaleInterface;
use Sylius\Component\Resource\Repository\RepositoryInterface;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * @author Jan Góralski <jan.goralski@lakion.com>
 */
class LocaleCodeChoiceType extends AbstractType
{
    /**
     * @var RepositoryInterface
     */
    protected $localeRepository;

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
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'choices' => $this->getLocalesCodes(),
            'choices_as_values' => true,
            'placeholder' => null,
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function getParent()
    {
        return 'choice';
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'sylius_locale_code_choice';
    }

    /**
     * @return array
     */
    private function getLocalesCodes()
    {
        $localesCodes = [];

        /** @var LocaleInterface $locale */
        foreach ($this->localeRepository->findAll() as $locale) {
            $localesCodes[$locale->getName()] = $locale->getCode();
        }

        return $localesCodes;
    }
}
