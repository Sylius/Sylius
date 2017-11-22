<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Sylius\Bundle\CoreBundle\Installer\Setup;

use Sylius\Component\Locale\Model\LocaleInterface;
use Sylius\Component\Resource\Factory\FactoryInterface;
use Sylius\Component\Resource\Repository\RepositoryInterface;
use Symfony\Component\Console\Helper\QuestionHelper;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\Question;
use Symfony\Component\Intl\Intl;

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
    public function __construct(RepositoryInterface $localeRepository, FactoryInterface $localeFactory, string $locale)
    {
        $this->localeRepository = $localeRepository;
        $this->localeFactory = $localeFactory;
        $this->locale = trim($locale);
    }

    /**
     * {@inheritdoc}
     */
    public function setup(InputInterface $input, OutputInterface $output, QuestionHelper $questionHelper): LocaleInterface
    {   
        $code = $this->getLocaleCodeFromUser($input, $output, $questionHelper);
        $name = $this->getLanguageName($code);

        $output->writeln(sprintf('Adding <info>%s</info> locale.', $name));

        /** @var LocaleInterface $existingLocale */
        $existingLocale = $this->localeRepository->findOneBy(['code' => $this->locale]);
        if (null !== $existingLocale) {
            return $existingLocale;
        }

        /** @var LocaleInterface $locale */
        $locale = $this->localeFactory->createNew();
        $locale->setCode($code);
        $this->localeRepository->add($locale);

        return $locale;
    }

    /**
     * @param string $code
     *
     * @return string|null
     */
    private function getLanguageName(string $code): ?string
    {
        return Intl::getLanguageBundle()->getLanguageName($code);
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @param QuestionHelper $questionHelper
     *
     * @return string
     */
    private function getLocaleCodeFromUser(InputInterface $input, OutputInterface $output, QuestionHelper $questionHelper): string
    {
        $code = $this->getNewLocaleCode($input, $output, $questionHelper);
        $name = $this->getLanguageName($code);

        while (null === $name) {
            $output->writeln(
                sprintf('<comment>Locale with code <info>%s</info> could not be resolved.</comment>', $code)
            );

            $code = $this->getNewLocaleCode($input, $output, $questionHelper);
            $name = $this->getLanguageName($code);
        }

        return $code;
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @param QuestionHelper $questionHelper
     *
     * @return string
     */
    private function getNewLocaleCode(InputInterface $input, OutputInterface $output, QuestionHelper $questionHelper): string
    {

        $question = new Question("Locale (press enter to use {$this->locale}): ", $this->locale);

        return trim($questionHelper->ask($input, $output, $question));
    }
}
