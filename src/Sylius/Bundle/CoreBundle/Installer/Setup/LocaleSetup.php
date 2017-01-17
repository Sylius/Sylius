<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\CoreBundle\Installer\Setup;

use Sylius\Component\Locale\Model\LocaleInterface;
use Sylius\Component\Resource\Factory\FactoryInterface;
use Sylius\Component\Resource\Repository\RepositoryInterface;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Intl\Intl;

/**
 * @author Mateusz Zalewski <mateusz.zalewski@lakion.com>
 */
final class LocaleSetup implements LocaleSetupInterface
{
    /**
     * @var RepositoryInterface
     */
    private $localeRepository;

    /**
     * @var FactoryInterface
     */
    private $localeFactory;

    /**
     * @var string
     */
    private $locale;

    /**
     * @param RepositoryInterface $localeRepository
     * @param FactoryInterface $localeFactory
     * @param string $locale
     */
    public function __construct(RepositoryInterface $localeRepository, FactoryInterface $localeFactory, $locale)
    {
        $this->localeRepository = $localeRepository;
        $this->localeFactory = $localeFactory;
        $this->locale = trim($locale);
    }

    /**
     * {@inheritdoc}
     */
    public function setup(InputInterface $input, OutputInterface $output)
    {
        $name = $this->getLanguageName($this->locale);

        $output->writeln(sprintf('Adding <info>%s</info> locale.', $name));

        $existingLocale = $this->localeRepository->findOneBy(['code' => $this->locale]);
        if (null !== $existingLocale) {
            return $existingLocale;
        }

        /** @var LocaleInterface $locale */
        $locale = $this->localeFactory->createNew();
        $locale->setCode($this->locale);

        $this->localeRepository->add($locale);

        return $locale;
    }

    /**
     * @param string $code
     *
     * @return string|null
     */
    private function getLanguageName($code)
    {
        return Intl::getLanguageBundle()->getLanguageName($code);
    }
}
