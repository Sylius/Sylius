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

namespace Sylius\Behat\Context\Cli;

use Behat\Behat\Context\Context;
use Sylius\Bundle\CoreBundle\Command\RemoveLocaleCommand;
use Sylius\Bundle\LocaleBundle\Checker\LocaleUsageCheckerInterface;
use Sylius\Component\Locale\Converter\LocaleConverterInterface;
use Sylius\Component\Resource\Repository\RepositoryInterface;
use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Component\Console\Tester\CommandTester;
use Symfony\Component\HttpKernel\KernelInterface;
use Webmozart\Assert\Assert;

final class RemoveLocaleContext implements Context
{
    private Application $application;

    private ?CommandTester $commandTester = null;

    public function __construct(
        KernelInterface $kernel,
        private RepositoryInterface $localeRepository,
        private LocaleConverterInterface $localeConverter,
        LocaleUsageCheckerInterface $localeUsageChecker,
    ) {
        $this->application = new Application($kernel);
        $this->application->add(new RemoveLocaleCommand($localeRepository, $localeUsageChecker));
    }

    /**
     * @When I remove :localeCode locale
     */
    public function iRunACommandRemovingTheLocale(string $localeName): void
    {
        $localeCode = $this->localeConverter->convertNameToCode($localeName);

        $command = $this->application->find('sylius:remove-locale');

        $this->commandTester = new CommandTester($command);
        $this->commandTester->setInputs(['locale' => $localeCode]);
        $this->commandTester->execute(['command' => $command->getName()]);
    }

    /**
     * @Then I should be informed that locale :localeName has been deleted
     */
    public function iShouldBeInformedThatLocaleHasBeenDeleted(string $localeName): void
    {
        $localeCode = $this->localeConverter->convertNameToCode($localeName);

        Assert::contains(
            $this->commandTester?->getDisplay(),
            sprintf('Locale "%s" has been successfully removed.', $localeCode),
        );
    }

    /**
     * @Then I should be informed that locale :localeName is in use and cannot be deleted
     */
    public function iShouldBeInformedThatLocaleIsInUseAndCannotBeDeleted(string $localeName): void
    {
        $localeCode = $this->localeConverter->convertNameToCode($localeName);

        Assert::contains(
            $this->commandTester?->getDisplay(),
            sprintf('Locale "%s" cannot be deleted, as it is used by at least one translation.', $localeCode),
        );
    }

    /**
     * @Then only the :localeName locale should be present in the system
     */
    public function onlyTheLocaleShouldBePresentInTheSystem(string $localeName): void
    {
        $localeCode = $this->localeConverter->convertNameToCode($localeName);

        $locales = $this->localeRepository->findAll();

        Assert::count($locales, 1);
        Assert::same($locales[0]->getCode(), $localeCode);
    }

    /**
     * @Then the :localeName locale should be still present in the system
     */
    public function theLocaleShouldBePresentInTheSystem(string $localeName): void
    {
        $localeCode = $this->localeConverter->convertNameToCode($localeName);

        $locale = $this->localeRepository->findOneBy(['code' => $localeCode]);

        Assert::notNull($locale);
    }
}
