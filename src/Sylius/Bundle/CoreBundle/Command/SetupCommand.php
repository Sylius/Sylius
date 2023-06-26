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

namespace Sylius\Bundle\CoreBundle\Command;

use Sylius\Bundle\CoreBundle\Installer\Setup\ChannelSetupInterface;
use Sylius\Bundle\CoreBundle\Installer\Setup\CurrencySetup;
use Sylius\Bundle\CoreBundle\Installer\Setup\CurrencySetupInterface;
use Sylius\Bundle\CoreBundle\Installer\Setup\LocaleSetupInterface;
use Sylius\Component\Core\Model\AdminUserInterface;
use Sylius\Component\User\Repository\UserRepositoryInterface;
use Symfony\Component\Console\Helper\QuestionHelper;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\Question;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Validator\Constraints\Email;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\ConstraintViolationListInterface;
use Webmozart\Assert\Assert;

final class SetupCommand extends AbstractInstallCommand
{
    protected static $defaultName = 'sylius:install:setup';

    protected function configure(): void
    {
        $this
            ->setDescription('Sylius configuration setup.')
            ->setHelp(
                <<<EOT
The <info>%command.name%</info> command allows user to configure basic Sylius data.
EOT
            )
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $currencySetup = $this->getContainer()->get('sylius.setup.currency');
        Assert::isInstanceOf($currencySetup, CurrencySetupInterface::class);
        $questionHelper = $this->getHelper('question');
        Assert::isInstanceOf($questionHelper, QuestionHelper::class);
        $currency = $currencySetup->setup($input, $output, $questionHelper);
        $localeSetup = $this->getContainer()->get('sylius.setup.locale');
        Assert::isInstanceOf($localeSetup, LocaleSetupInterface::class);
        $locale = $localeSetup->setup($input, $output, $questionHelper);
        $channelSetup = $this->getContainer()->get('sylius.setup.channel');
        Assert::isInstanceOf($channelSetup, ChannelSetupInterface::class);
        $channelSetup->setup($locale, $currency);
        $this->setupAdministratorUser($input, $output, $locale->getCode());

        return 0;
    }

    protected function setupAdministratorUser(InputInterface $input, OutputInterface $output, string $localeCode): void
    {
        $outputStyle = new SymfonyStyle($input, $output);
        $outputStyle->writeln('Create your administrator account.');

        $userManager = $this->getContainer()->get('sylius.manager.admin_user');
        $userFactory = $this->getContainer()->get('sylius.factory.admin_user');

        try {
            $adminUser = $userFactory->createNew();
            Assert::isInstanceOf($adminUser, AdminUserInterface::class);
            $user = $this->configureNewUser($adminUser, $input, $output);
        } catch (\InvalidArgumentException) {
            return;
        }

        $user->setEnabled(true);
        $user->setLocaleCode($localeCode);

        $userManager->persist($user);
        $userManager->flush();

        $outputStyle->writeln('<info>Administrator account successfully registered.</info>');
        $outputStyle->newLine();
    }

    private function configureNewUser(
        AdminUserInterface $user,
        InputInterface $input,
        OutputInterface $output,
    ): AdminUserInterface {
        /** @var UserRepositoryInterface $userRepository */
        $userRepository = $this->getAdminUserRepository();

        if ($input->getOption('no-interaction')) {
            Assert::null($userRepository->findOneByEmail('sylius@example.com'));

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
                    /** @var ConstraintViolationListInterface $errors */
                    $errors = $this->getContainer()->get('validator')->validate((string) $value, [new Email(), new NotBlank()]);
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
        /** @var UserRepositoryInterface $userRepository */
        $userRepository = $this->getAdminUserRepository();

        do {
            $question = $this->createEmailQuestion();
            $email = $questionHelper->ask($input, $output, $question);
            $exists = null !== $userRepository->findOneByEmail($email);

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
        /** @var UserRepositoryInterface $userRepository */
        $userRepository = $this->getAdminUserRepository();

        do {
            $question = new Question('Username (press enter to use email): ', $email);
            $username = $questionHelper->ask($input, $output, $question);
            $exists = null !== $userRepository->findOneBy(['username' => $username]);

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
                /** @var ConstraintViolationListInterface $errors */
                $errors = $this->getContainer()->get('validator')->validate($value, [new NotBlank()]);
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

    private function getAdminUserRepository(): UserRepositoryInterface
    {
        return $this->getContainer()->get('sylius.repository.admin_user');
    }
}
