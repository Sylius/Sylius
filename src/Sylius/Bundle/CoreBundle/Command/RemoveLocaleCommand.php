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

namespace Sylius\Bundle\CoreBundle\Command;

use Sylius\Bundle\LocaleBundle\Checker\LocaleUsageCheckerInterface;
use Sylius\Component\Locale\Model\LocaleInterface;
use Sylius\Component\Resource\Repository\RepositoryInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Helper\QuestionHelper;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\ChoiceQuestion;

final class RemoveLocaleCommand extends Command
{
    private const NAME = 'sylius:remove-locale';

    public function __construct (
        private RepositoryInterface $localeRepository,
        private LocaleUsageCheckerInterface $localeUsageChecker,
    ) {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->setName(self::NAME)
            ->setDescription('Remove a locale from Sylius')
            ->addArgument(name: 'locale', description: 'Locale code to remove')
            ->setHelp(
                <<<EOT
                The <info>%command.name%</info> command removes a locale from Sylius.
                EOT
            )
        ;
    }

    protected function interact(InputInterface $input, OutputInterface $output): void
    {
        $helper = $this->getHelper('question');
        if (!$helper instanceof QuestionHelper) {
            throw new \RuntimeException('Helper must be an instance of QuestionHelper');
        }


        if (null === $input->getArgument('locale')) {
            $localeForDeletion = $helper->ask($input, $output, $this->createLocaleForDeletionChoiceQuestion());
            $input->setArgument('locale', $localeForDeletion);
        }
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $localeForDeletion = $input->getArgument('locale');

        try {
            $this->processLocaleDeletion($localeForDeletion);
        } catch (\RuntimeException $exception) {
            $output->writeln(sprintf('<error>%s</error>', $exception->getMessage()));

            return self::FAILURE;
        }

        $output->writeln(sprintf('Locale "%s" has been successfully removed.', $localeForDeletion));

        return self::SUCCESS;
    }

    private function createLocaleForDeletionChoiceQuestion(): ChoiceQuestion
    {
        $choices = [];
        $locales = $this->localeRepository->findAll();

        /** @var LocaleInterface $locale */
        foreach ($locales as $locale) {
            $choices[$locale->getCode()] = $locale->getName();
        }

        return new ChoiceQuestion(
            'Please provide a locale to remove',
            $choices,
        );
    }

    private function processLocaleDeletion(string $localeCode): void
    {
        /** @var LocaleInterface|null $locale */
        $locale = $this->localeRepository->findOneBy(['code' => $localeCode]);

        if (null === $locale) {
            throw new \RuntimeException(sprintf('Locale "%s" does not exist.', $localeCode));
        }

        if ($this->localeUsageChecker->isUsed($locale->getCode())) {
            throw new \RuntimeException(
                sprintf('Locale "%s" cannot be deleted, as it is used by at least one translation.', $locale->getCode())
            );
        }

        $this->localeRepository->remove($locale);
    }
}
