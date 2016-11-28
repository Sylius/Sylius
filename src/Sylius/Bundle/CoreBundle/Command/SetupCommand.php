<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\CoreBundle\Command;

use Sylius\Component\Core\Model\AdminUserInterface;
use Sylius\Component\Core\Model\ChannelInterface;
use Sylius\Component\Currency\Model\CurrencyInterface;
use Sylius\Component\Locale\Model\LocaleInterface;
use Symfony\Component\Console\Helper\QuestionHelper;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\Question;
use Symfony\Component\Intl\Intl;
use Symfony\Component\Validator\Constraints\Email;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\ConstraintViolationListInterface;

/**
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 */
final class SetupCommand extends AbstractInstallCommand
{
    /**
     * @var CurrencyInterface
     */
    private $currency;

    /**
     * @var LocaleInterface
     */
    private $locale;

    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this
            ->setName('sylius:install:setup')
            ->setDescription('Sylius configuration setup.')
            ->setHelp(<<<EOT
The <info>%command.name%</info> command allows user to configure basic Sylius data.
EOT
            )
        ;
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->setupCurrency($input, $output);
        $this->setupLocale($input, $output);
        $this->setupChannel();
        $this->setupAdministratorUser($input, $output);
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     *
     * @return int
     */
    protected function setupAdministratorUser(InputInterface $input, OutputInterface $output)
    {
        /** @var QuestionHelper $questionHelper */
        $questionHelper = $this->getHelper('question');

        $output->writeln('Create your administrator account.');

        $userManager = $this->get('sylius.manager.admin_user');
        $userRepository = $this->get('sylius.repository.admin_user');
        $userFactory = $this->get('sylius.factory.admin_user');

        /** @var AdminUserInterface $user */
        $user = $userFactory->createNew();

        if ($input->getOption('no-interaction')) {
            $exists = null !== $userRepository->findOneByEmail('sylius@example.com');

            if ($exists) {
                return 0;
            }

            $user->setEmail('sylius@example.com');
            $user->setPlainPassword('sylius');
        } else {
            do {
                $question = new Question('E-mail: ');
                $question->setValidator(function ($value) use ($output) {
                    /** @var ConstraintViolationListInterface $errors */
                    $errors = $this->get('validator')->validate((string) $value, [new Email(), new NotBlank()]);
                    foreach ($errors as $error) {
                        $output->writeln(sprintf('<error>%s</error>', $error->getMessage()));
                    }

                    return count($errors) === 0;
                });
                $email = $questionHelper->ask($input, $output, $question);
                $exists = null !== $userRepository->findOneByEmail($email);

                if ($exists) {
                    $output->writeln('<error>E-Mail is already in use!</error>');
                }
            } while ($exists);

            $user->setEmail($email);
            $user->setPlainPassword($this->getAdministratorPassword($input, $output));
        }

        $user->setEnabled(true);
        $user->setLocaleCode($this->locale->getCode());

        $userManager->persist($user);
        $userManager->flush();
        $output->writeln('Administrator account successfully registered.');
    }

    /**
     * @param InputInterface  $input
     * @param OutputInterface $output
     */
    protected function setupLocale(InputInterface $input, OutputInterface $output)
    {
        $localeRepository = $this->get('sylius.repository.locale');
        $localeManager = $this->get('sylius.manager.locale');
        $localeFactory = $this->get('sylius.factory.locale');

        $code = trim($this->getContainer()->getParameter('locale'));
        $name = Intl::getLanguageBundle()->getLanguageName($code);
        $output->writeln(sprintf('Adding <info>%s</info> locale.', $name));

        if (null !== $existingLocale = $localeRepository->findOneBy(['code' => $code])) {
            $this->locale = $existingLocale;

            return;
        }

        $locale = $localeFactory->createNew();
        $locale->setCode($code);

        $localeManager->persist($locale);
        $localeManager->flush();

        $this->locale = $locale;
    }

    /**
     * @param InputInterface  $input
     * @param OutputInterface $output
     */
    protected function setupCurrency(InputInterface $input, OutputInterface $output)
    {
        /** @var QuestionHelper $questionHelper */
        $questionHelper = $this->getHelper('question');
        $question = new Question('Currency (press enter to use USD): ', 'USD');

        $currencyRepository = $this->get('sylius.repository.currency');
        $currencyManager = $this->get('sylius.manager.currency');
        $currencyFactory = $this->get('sylius.factory.currency');

        $code = trim($questionHelper->ask($input, $output, $question));

        $name = Intl::getCurrencyBundle()->getCurrencyName($code);
        $output->writeln(sprintf('Adding <info>%s</info> currency.', $name));

        if (null !== $existingCurrency = $currencyRepository->findOneBy(['code' => $code])) {
            $this->currency = $existingCurrency;

            return;
        }

        $currency = $currencyFactory->createNew();
        $currency->setCode($code);

        $currencyManager->persist($currency);
        $currencyManager->flush();

        $this->currency = $currency;
    }

    protected function setupChannel()
    {
        $channelRepository = $this->get('sylius.repository.channel');
        $channelManager = $this->get('sylius.manager.channel');
        $channelFactory = $this->get('sylius.factory.channel');

        /** @var ChannelInterface $channel */
        $channel = $channelRepository->findOneBy([]);

        if (null === $channel) {
            $channel = $channelFactory->createNew();
            $channel->setCode('default');
            $channel->setName('Default');
            $channel->setTaxCalculationStrategy('order_items_based');

            $channelManager->persist($channel);
        }

        $channel->addCurrency($this->currency);
        $channel->addLocale($this->locale);
        $channel->setBaseCurrency($this->currency);
        $channel->setDefaultLocale($this->locale);

        $channelManager->flush();
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     *
     * @return mixed
     */
    private function getAdministratorPassword(InputInterface $input, OutputInterface $output)
    {
        /** @var QuestionHelper $questionHelper */
        $questionHelper = $this->getHelper('question');

        $validator = function ($value) use ($output) {
            /** @var ConstraintViolationListInterface $errors */
            $errors = $this->get('validator')->validate($value, [new NotBlank()]);
            foreach ($errors as $error) {
                $output->writeln(sprintf('<error>%s</error>', $error->getMessage()));
            }

            return count($errors) === 0;
        };

        do {
            $passwordQuestion = (new Question('Choose password: '))
                ->setValidator($validator)
                ->setHidden(true)
                ->setHiddenFallback(false)
            ;
            $confirmPasswordQuestion = (new Question('Confirm password: '))
                ->setValidator($validator)
                ->setHidden(true)
                ->setHiddenFallback(false)
            ;

            $password = $questionHelper->ask($input, $output, $passwordQuestion);
            $repeatedPassword = $questionHelper->ask($input, $output, $confirmPasswordQuestion);

            if ($repeatedPassword !== $password) {
                $output->writeln('<error>Passwords do not match!</error>');
            }
        } while ($repeatedPassword !== $password);

        return $password;
    }
}
