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

namespace Sylius\Bundle\CoreBundle\Console\Command;

use Doctrine\ORM\EntityManagerInterface;
use Sylius\Bundle\CoreBundle\Installer\Checker\CommandDirectoryChecker;
use Sylius\Bundle\CoreBundle\Installer\Setup\ChannelSetupInterface;
use Sylius\Bundle\CoreBundle\Installer\Setup\CurrencySetupInterface;
use Sylius\Bundle\CoreBundle\Installer\Setup\LocaleSetupInterface;
use Sylius\Component\Core\Model\AdminUserInterface;
use Sylius\Component\User\Repository\UserRepositoryInterface;
use Sylius\Resource\Factory\FactoryInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Helper\QuestionHelper;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\Question;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Validator\Constraints\Email;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Webmozart\Assert\Assert;

#[AsCommand(
    name: 'sylius:install:setup',
    description: 'Sylius configuration setup.',
)]
final class SetupCommand extends AbstractInstallCommand
{
    /**
     * @param FactoryInterface<AdminUserInterface> $adminUserFactory
     * @param UserRepositoryInterface<AdminUserInterface> $adminUserRepository
     */
    public function __construct(
        protected readonly EntityManagerInterface $entityManager,
        protected readonly CommandDirectoryChecker $commandDirectoryChecker,
        protected readonly CurrencySetupInterface $currencySetup,
        protected readonly LocaleSetupInterface $localeSetup,
        protected readonly ChannelSetupInterface $channelSetup,
        protected readonly FactoryInterface $adminUserFactory,
        protected readonly UserRepositoryInterface $adminUserRepository,
        protected readonly ValidatorInterface $validator,
    ) {
        parent::__construct($this->entityManager, $this->commandDirectoryChecker);
    }

    protected function configure(): void
    {
        $this
            ->setHelp(
                <<<EOT
The <info>%command.name%</info> command allows user to configure basic Sylius data.
EOT
            )
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        /** @var QuestionHelper $questionHelper */
        $questionHelper = $this->getHelper('question');

        $currency = $this->currencySetup->setup($input, $output, $questionHelper);
        $locale = $this->localeSetup->setup($input, $output, $questionHelper);
        $this->channelSetup->setup($locale, $currency);
        $this->setupAdministratorUser($input, $output, $locale->getCode());

        return Command::SUCCESS;
    }

    protected function setupAdministratorUser(InputInterface $input, OutputInterface $output, string $localeCode): void
    {
        $outputStyle = new SymfonyStyle($input, $output);
        $outputStyle->writeln('Create your administrator account.');

        try {
            $user = $this->configureNewUser($this->adminUserFactory->createNew(), $input, $output);
        } catch (\InvalidArgumentException) {
            return;
        }

        $user->setEnabled(true);
        $user->setLocaleCode($localeCode);

        $this->entityManager->persist($user);
        $this->entityManager->flush();

        $outputStyle->writeln('<info>Administrator account successfully registered.</info>');
        $outputStyle->newLine();
    }

    private function configureNewUser(
        AdminUserInterface $user,
        InputInterface $input,
        OutputInterface $output,
    ): AdminUserInterface {
        if ($input->getOption('no-interaction')) {
            Assert::null($this->adminUserRepository->findOneByEmail('sylius@example.com'));

            $user->setEmail('sylius@example.com');
            $user->setUsername('sylius');
            $user->setPlainPassword('sylius');

            return $user;
        }

        $email = $this->getAdministratorEmail($input, $output);
        $user->setEmail($email);
        $user->setUsername($this->getAdministratorUsername($input, $output, $email));
        $user->setPlainPassword($this->getAdministratorPassword($input, $output));

        return $user;
    }

    private function createEmailQuestion(): Question
    {
        return (new Question('E-mail: '))
            ->setValidator(
                /**
                 * @param mixed $value
                 */
                function ($value): string {
                    $errors = $this->validator->validate((string) $value, [new Email(), new NotBlank()]);
                    foreach ($errors as $error) {
                        throw new \DomainException((string) $error->getMessage());
                    }

                    return $value;
                },
            )
            ->setMaxAttempts(3)
        ;
    }

    private function getAdministratorEmail(InputInterface $input, OutputInterface $output): string
    {
        /** @var QuestionHelper $questionHelper */
        $questionHelper = $this->getHelper('question');

        do {
            $question = $this->createEmailQuestion();
            $email = $questionHelper->ask($input, $output, $question);
            $exists = null !== $this->adminUserRepository->findOneByEmail($email);

            if ($exists) {
                $output->writeln('<error>E-Mail is already in use!</error>');
            }
        } while ($exists);

        return $email;
    }

    private function getAdministratorUsername(InputInterface $input, OutputInterface $output, string $email): string
    {
        /** @var QuestionHelper $questionHelper */
        $questionHelper = $this->getHelper('question');

        do {
            $question = new Question('Username (press enter to use email): ', $email);
            $username = $questionHelper->ask($input, $output, $question);
            $exists = null !== $this->adminUserRepository->findOneBy(['username' => $username]);

            if ($exists) {
                $output->writeln('<error>Username is already in use!</error>');
            }
        } while ($exists);

        return $username;
    }

    private function getAdministratorPassword(InputInterface $input, OutputInterface $output): string
    {
        /** @var QuestionHelper $questionHelper */
        $questionHelper = $this->getHelper('question');
        $validator = $this->getPasswordQuestionValidator();

        do {
            $passwordQuestion = $this->createPasswordQuestion('Choose password:', $validator);
            $confirmPasswordQuestion = $this->createPasswordQuestion('Confirm password:', $validator);

            $password = $questionHelper->ask($input, $output, $passwordQuestion);
            $repeatedPassword = $questionHelper->ask($input, $output, $confirmPasswordQuestion);

            if ($repeatedPassword !== $password) {
                $output->writeln('<error>Passwords do not match!</error>');
            }
        } while ($repeatedPassword !== $password);

        return $password;
    }

    private function getPasswordQuestionValidator(): \Closure
    {
        return
            /** @param mixed $value */
            function ($value): string {
                $errors = $this->validator->validate($value, [new NotBlank()]);
                foreach ($errors as $error) {
                    throw new \DomainException((string) $error->getMessage());
                }

                return $value;
            }
        ;
    }

    private function createPasswordQuestion(string $message, \Closure $validator): Question
    {
        return (new Question($message))
            ->setValidator($validator)
            ->setMaxAttempts(3)
            ->setHidden(true)
            ->setHiddenFallback(false)
        ;
    }
}
