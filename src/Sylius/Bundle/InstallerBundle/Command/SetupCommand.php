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

use Doctrine\Common\Collections\ArrayCollection;
use Sylius\Component\Core\Model\ChannelInterface;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Intl\Exception\MethodNotImplementedException;
use Symfony\Component\Intl\Intl;
use Symfony\Component\Validator\Constraints\Country;
use Symfony\Component\Validator\Constraints\Currency;
use Symfony\Component\Validator\Constraints\Email;
use Symfony\Component\Validator\Constraints\Locale;
use Symfony\Component\Validator\Constraints\NotBlank;

class SetupCommand extends AbstractInstallCommand
{
    /**
     * @var array
     */
    private $currencies = [];

    /**
     * @var array
     */
    private $locales = [];

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
        $this->setupLocales($input, $output);
        $this->setupCurrencies($input, $output);
        $this->setupCountries($input, $output);
        $this->setupChannels($input, $output);
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
    }

    /**
     * @param InputInterface  $input
     * @param OutputInterface $output
     */
    protected function setupLocales(InputInterface $input, OutputInterface $output)
    {
        $localeRepository = $this->get('sylius.repository.locale');
        $localeManager = $this->get('sylius.manager.locale');
        $localeFactory = $this->get('sylius.factory.locale');

        do {
            $locales = $this->getLocalesCodes($input, $output);

            $valid = true;

            foreach ($locales as $code) {
                if (0 !== count($errors = $this->validate(trim($code), [new Locale()]))) {
                    $valid = false;
                }

                $this->writeErrors($output, $errors);
            }
        } while (!$valid);

        foreach ($locales as $key => $code) {
            $code = trim($code);

            try {
                $name = \Locale::getDisplayName($code);
            } catch (MethodNotImplementedException $e) {
                $name = $code;
            }

            $output->writeln(sprintf('Adding <info>%s</info>.', $name));

            if (null !== $existingLocale = $localeRepository->findOneByCode($code)) {
                $this->locales[] = $existingLocale;

                continue;
            }

            $locale = $localeFactory->createNew();
            $locale->setCode($code);
            $this->locales[] = $locale;

            $localeManager->persist($locale);
        }

        $localeManager->flush();
    }

    /**
     * @param InputInterface  $input
     * @param OutputInterface $output
     */
    protected function setupCurrencies(InputInterface $input, OutputInterface $output)
    {
        $currencyRepository = $this->get('sylius.repository.currency');
        $currencyManager = $this->get('sylius.manager.currency');
        $currencyFactory = $this->get('sylius.factory.currency');

        do {
            $currencies = $this->getCurrenciesCodes($input, $output);

            $valid = true;

            foreach ($currencies as $code) {
                if (0 !== count($errors = $this->validate(trim($code), [new Currency()]))) {
                    $valid = false;
                }

                $this->writeErrors($output, $errors);
            }
        } while (!$valid);

        foreach ($currencies as $key => $code) {
            $code = trim($code);

            $name = Intl::getCurrencyBundle()->getCurrencyName($code);
            $output->writeln(sprintf('Adding <info>%s</info>.', $name));

            if (null !== $existingCurrency = $currencyRepository->findOneByCode($code)) {
                $this->currencies[] = $existingCurrency;

                continue;
            }

            $currency = $currencyFactory->createNew();
            $currency->setCode($code);
            $currency->setExchangeRate(1);
            $this->currencies[] = $currency;

            $currencyManager->persist($currency);
        }

        $currencyManager->flush();
    }

    /**
     * @param InputInterface  $input
     * @param OutputInterface $output
     */
    protected function setupCountries(InputInterface $input, OutputInterface $output)
    {
        $countryRepository = $this->get('sylius.repository.country');
        $countryManager = $this->get('sylius.manager.country');
        $countryFactory = $this->get('sylius.factory.country');

        do {
            $countries = $this->getCountriesCodes($input, $output);

            $valid = true;

            foreach ($countries as $code) {
                if (0 !== count($errors = $this->validate(trim($code), [new Country()]))) {
                    $valid = false;
                }

                $this->writeErrors($output, $errors);
            }
        } while (!$valid);

        foreach ($countries as $key => $code) {
            $code = trim($code);
            $name = Intl::getRegionBundle()->getCountryName($code);

            $output->writeln(sprintf('Adding <info>%s</info>.', $name));

            if (null !== $countryRepository->findOneByCode($code)) {
                continue;
            }

            $country = $countryFactory->createNew();
            $country->setCode($code);

            $countryManager->persist($country);
        }

        $countryManager->flush();
    }

    /**
     * @param InputInterface  $input
     * @param OutputInterface $output
     */
    protected function setupChannels(InputInterface $input, OutputInterface $output)
    {
        $channelRepository = $this->get('sylius.repository.channel');
        $channelManager = $this->get('sylius.manager.channel');
        $channelFactory = $this->get('sylius.factory.channel');

        $channels = $this->getChannelsCodes($input, $output);

        foreach ($channels as $code) {
            $output->writeln(sprintf('Adding <info>%s</info>.', $code));

            if (null !== $channelRepository->findOneByCode($code)) {
                continue;
            }

            /** @var ChannelInterface $channel */
            $channel = $channelFactory->createNew();
            $channel->setHostname(null);
            $channel->setCode($code);
            $channel->setName($code);
            $channel->setColor(null);
            $channel->setCurrencies(new ArrayCollection($this->currencies));
            $channel->setLocales(new ArrayCollection($this->locales));

            $channelManager->persist($channel);
        }

        $channelManager->flush();
    }

    /**
     * @param InputInterface  $input
     * @param OutputInterface $output
     *
     * @return array
     */
    private function getChannelsCodes(InputInterface $input, OutputInterface $output)
    {
        return $this->getCodes(
            $input,
            $output,
            'On which channels are you going to sell your goods?',
            'Please enter a list of channels, separated by commas or just hit ENTER to use "DEFAULT". For example "WEB-UK, WEB-DE, MOBILE".',
            'DEFAULT'
        );
    }

    /**
     * @param InputInterface  $input
     * @param OutputInterface $output
     *
     * @return array
     */
    private function getCurrenciesCodes(InputInterface $input, OutputInterface $output)
    {
        return $this->getCodes(
            $input,
            $output,
            'In which currency your customers can buy goods?',
            'Please enter list of currency codes, separated by commas or just hit ENTER to use "USD". For example "USD, EUR, GBP".',
            'USD'
        );
    }

    /**
     * @param InputInterface  $input
     * @param OutputInterface $output
     *
     * @return array
     */
    private function getLocalesCodes(InputInterface $input, OutputInterface $output)
    {
        return $this->getCodes(
            $input,
            $output,
            'In which language your customers can browse the store?',
            'Please enter list of locale codes, separated by commas or just hit ENTER to use "en_US". For example "en_US, de_DE".',
            'en_US'
        );
    }

    /**
     * @param InputInterface  $input
     * @param OutputInterface $output
     *
     * @return array
     */
    private function getCountriesCodes(InputInterface $input, OutputInterface $output)
    {
        return $this->getCodes(
            $input,
            $output,
            'To which countries you are going to sell your goods?',
            'Please enter list of country codes, separated by commas or just hit ENTER to use "US". For example "US, PL, DE".',
            'US'
        );
    }

    /**
     * @param InputInterface  $input
     * @param OutputInterface $output
     * @param string          $question
     * @param string          $description
     * @param string          $defaultAnswer
     *
     * @return array
     */
    private function getCodes(InputInterface $input, OutputInterface $output, $question, $description, $defaultAnswer)
    {
        if ($input->getOption('no-interaction')) {
            return [$defaultAnswer];
        }

        $output->writeln($description);
        $codes = $this->ask($output, '<question>'.$question.'</question> ', [], $defaultAnswer);

        return explode(',', $codes);
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
            $repeatedPassword = $this->askHidden($output, 'Repeat password:', [new NotBlank()]);

            if ($repeatedPassword !== $password) {
                $output->writeln('<error>Passwords does not match confirmation!</error>');
            }
        } while ($repeatedPassword !== $password);

        return $password;
    }
}
