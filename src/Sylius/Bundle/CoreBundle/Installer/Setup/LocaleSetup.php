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

namespace Sylius\Bundle\CoreBundle\Installer\Setup;

use Sylius\Component\Locale\Model\LocaleInterface;
use Sylius\Component\Resource\Factory\FactoryInterface;
use Sylius\Component\Resource\Repository\RepositoryInterface;
use Symfony\Component\Console\Helper\QuestionHelper;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\Question;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Intl\Exception\MissingResourceException;
use Symfony\Component\Intl\Languages;
use Symfony\Component\Yaml\Yaml;

final class LocaleSetup implements LocaleSetupInterface
{
    public function __construct(
        private RepositoryInterface $localeRepository,
        private FactoryInterface $localeFactory,
        private string $locale,
        private ?Filesystem $filesystem = null,
        private ?string $localeParameterFilePath = 'config/parameters.yaml',
    ) {
        $this->locale = trim($locale);

        if (null === $this->filesystem) {
            trigger_deprecation('sylius/sylius', '1.13', 'Not passing %s to %s constructor is deprecated. It will be required in Sylius 2.0.', Filesystem::class, self::class);
        }

        if (null === $this->localeParameterFilePath) {
            trigger_deprecation('sylius/sylius', '1.13', 'Not passing $localeParameterFilePath to %s constructor is deprecated. It will be required in Sylius 2.0.', self::class);
        }
    }

    public function setup(InputInterface $input, OutputInterface $output, QuestionHelper $questionHelper): LocaleInterface
    {
        $code = $this->getLanguageCodeFromUser($input, $output, $questionHelper);

        $output->writeln(sprintf('Adding <info>%s</info> locale.', $code));

        /** @var LocaleInterface|null $existingLocale */
        $existingLocale = $this->localeRepository->findOneBy(['code' => $code]);
        if (null !== $existingLocale) {
            return $existingLocale;
        }

        /** @var LocaleInterface $locale */
        $locale = $this->localeFactory->createNew();
        $locale->setCode($code);

        $this->localeRepository->add($locale);

        return $locale;
    }

    private function getLanguageCodeFromUser(InputInterface $input, OutputInterface $output, QuestionHelper $questionHelper): string
    {
        $code = $this->getNewLanguageCode($input, $output, $questionHelper);
        $name = $this->getLanguageName($code);

        while (null === $name) {
            $output->writeln(
                sprintf('<comment>Language with code <info>%s</info> could not be resolved.</comment>', $code),
            );

            $code = $this->getNewLanguageCode($input, $output, $questionHelper);
            $name = $this->getLanguageName($code);
        }

        $this->updateLocaleParameter($code, $output);

        $output->writeln(sprintf('Adding <info>%s</info> Language.', $name));

        return $code;
    }

    private function getNewLanguageCode(InputInterface $input, OutputInterface $output, QuestionHelper $questionHelper): string
    {
        $question = new Question('Language (press enter to use ' . $this->locale . '): ', $this->locale);

        return trim($questionHelper->ask($input, $output, $question));
    }

    private function getLanguageName(string $code): ?string
    {
        $language = $code;
        $region = null;

        if (count(explode('_', $code, 2)) === 2) {
            $codeParts = explode('_', $code, 2);
            $language = $codeParts[0];
            $region = $codeParts[1] ?? null;
        }

        try {
            return Languages::getName($language, $region);
        } catch (MissingResourceException) {
            return null;
        }
    }

    private function updateLocaleParameter(string $code, OutputInterface $output): void
    {
        if (
            $this->localeParameterFilePath === null ||
            $this->filesystem === null ||
            !$this->filesystem->exists($this->localeParameterFilePath) ||
            !is_writable($this->localeParameterFilePath)
        ) {
            $output->writeln('<info>You may also need to add this locale into config/parameters.yaml configuration.</info>');

            return;
        }

        $content = Yaml::parseFile($this->localeParameterFilePath);
        $content['parameters']['locale'] = $code;

        $this->filesystem->dumpFile($this->localeParameterFilePath, Yaml::dump($content));
    }
}
