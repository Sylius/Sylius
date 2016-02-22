<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\InstallerBundle\Command;

use Sylius\Component\Core\Model\ChannelInterface;
use Sylius\Component\Currency\Model\CurrencyInterface;
use Sylius\Component\Locale\Model\LocaleInterface;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Intl\Intl;
use Symfony\Component\Validator\Constraints\Currency;
use Symfony\Component\Validator\Constraints\Email;
use Symfony\Component\Validator\Constraints\NotBlank;

/**
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 */
class SetupCommand extends AbstractInstallCommand
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
        $output->writeln('Create your administrator account.');

        $userManager = $this->get('sylius.manager.user');
        $userRepository = $this->get('sylius.repository.user');
        $userFactory = $this->get('sylius.factory.user');
        $customerFactory = $this->get('sylius.factory.customer');

        $rbacInitializer = $this->get('sylius.rbac.initializer');
        $rbacInitializer->initialize();

        $user = $userFactory->createNew();
        $customer = $customerFactory->createNew();
        $user->setCustomer($customer);

        if ($input->getOption('no-interaction')) {
            $exists = null !== $userRepository->findOneByEmail('sylius@example.com');

            if ($exists) {
                return 0;
            }

            $customer->setFirstname('Sylius');
            $customer->setLastname('Admin');
            $user->setEmail('sylius@example.com');
            $user->setPlainPassword('sylius');
        } else {
            $customer->setFirstname($this->ask($output, 'Your firstname:', [new NotBlank()]));
            $customer->setLastname($this->ask($output, 'Lastname:', [new NotBlank()]));

            do {
                $email = $this->ask($output, 'E-Mail:', [new NotBlank(), new Email()]);
                $exists = null !== $userRepository->findOneByEmail($email);

                if ($exists) {
                    $output->writeln('<error>E-Mail is already in use!</error>');
                }
            } while ($exists);

            $user->setEmail($email);
            $user->setPlainPassword($this->getAdministratorPassword($output));
        }

        $user->setEnabled(true);
        $user->addAuthorizationRole($this->get('sylius.repository.role')->findOneBy(['code' => 'administrator']));

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

            $localeManager->flush();

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
        $currencyRepository = $this->get('sylius.repository.currency');
        $currencyManager = $this->get('sylius.manager.currency');
        $currencyFactory = $this->get('sylius.factory.currency');

        $code = trim($this->getContainer()->getParameter('currency'));
        $name = Intl::getCurrencyBundle()->getCurrencyName($code);
        $output->writeln(sprintf('Adding <info>%s</info> currency.', $name));

        if (null !== $existingCurrency = $currencyRepository->findOneBy(['code' => $code])) {
            $this->currency = $existingCurrency;
            $existingCurrency->setBase(true);

            $currencyManager->flush();

            return;
        }

        $currency = $currencyFactory->createNew();

        $currency->setExchangeRate(1);
        $currency->setBase(true);
        $currency->setCode($code);

        $this->currency = $currency;

        $currencyManager->persist($currency);
        $currencyManager->flush();

        $this->currency = $currency;
    }

    protected function setupChannel()
    {
        $channelRepository = $this->get('sylius.repository.channel');
        $channelManager = $this->get('sylius.manager.channel');
        $channelFactory = $this->get('sylius.factory.channel');

        $channel = $channelRepository->findOneByCode('default');

        if (null !== $channel) {
            return;
        }

        /** @var ChannelInterface $channel */
        $channel = $channelFactory->createNew();
        $channel->setCode('default');
        $channel->setName('Default');

        $channel->addCurrency($this->currency);
        $channel->addLocale($this->locale);

        $channelManager->persist($channel);
        $channelManager->flush();
    }

    /**
     * @param OutputInterface $output
     *
     * @return mixed
     */
    private function getAdministratorPassword(OutputInterface $output)
    {
        do {
            $password = $this->askHidden($output, 'Choose password:', [new NotBlank()]);
            $repeatedPassword = $this->askHidden($output, 'Confirm password:', [new NotBlank()]);

            if ($repeatedPassword !== $password) {
                $output->writeln('<error>Passwords do not match!</error>');
            }
        } while ($repeatedPassword !== $password);

        return $password;
    }
}
