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

        $rbacInitializer = $this->get('sylius.rbac.initializer');
        $rbacInitializer->initialize();

        $user = $userRepository->createNew();

        if ($input->getOption('no-interaction')) {
            $exists = null !== $userRepository->findOneByEmail('sylius@example.com');

            if ($exists) {
                return 0;
            }

            $user->setFirstname('Sylius');
            $user->setLastname('Admin');
            $user->setEmail('sylius@example.com');
            $user->setPlainPassword('sylius');
        } else {
            $user->setFirstname($this->ask($output, 'Your firstname:', array(new NotBlank())));
            $user->setLastname($this->ask($output, 'Lastname:', array(new NotBlank())));

            do {
                $email = $this->ask($output, 'E-Mail:', array(new NotBlank(), new Email()));
                $exists = null !== $userRepository->findOneByEmail($email);

                if ($exists) {
                    $output->writeln('<error>E-Mail is already in use!</error>');
                }
            } while ($exists);

            $user->setEmail($email);
            $user->setPlainPassword($this->ask($output, 'Choose password:', array(new NotBlank())));
        }

        $user->setEnabled(true);
        $user->addAuthorizationRole($this->get('sylius.repository.role')->findOneBy(array('code' => 'administrator')));

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

        do {
            if ($input->getOption('no-interaction')) {
                $locales = array('en_US');
            } else {
                $output->writeln('Please enter list of locale codes, separated by commas or just hit ENTER to use "en_US". For example "en_US, de_DE".');
                $codes = $this->ask($output, '<question>In which language your customers can browse the store?</question> ', array(), 'en_US');
                $locales = explode(',', $codes);
            }

            $valid = true;

            foreach ($locales as $code) {
                if (0 !== count($errors = $this->validate(trim($code), array(new Locale())))) {
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

            if (null !== $localeRepository->findOneByCode($code)) {
                continue;
            }

            $locale = $localeRepository->createNew();
            $locale->setCode($code);

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

        do {
            if ($input->getOption('no-interaction')) {
                $currencies = array('USD');
            } else {
                $output->writeln('Please enter list of currency codes, separated by commas or just hit ENTER to use "USD". For example "USD, EUR, GBP".');
                $codes = $this->ask($output, '<question>In which currency your customers can buy goods?</question> ', array(), 'USD');
                $currencies = explode(',', $codes);
            }

            $valid = true;

            foreach ($currencies as $code) {
                if (0 !== count($errors = $this->validate(trim($code), array(new Currency())))) {
                    $valid = false;
                }

                $this->writeErrors($output, $errors);
            }
        } while (!$valid);

        foreach ($currencies as $key => $code) {
            $code = trim($code);

            $name = Intl::getCurrencyBundle()->getCurrencyName($code);
            $output->writeln(sprintf('Adding <info>%s</info>.', $name));

            if (null !== $currencyRepository->findOneByCode($code)) {
                continue;
            }

            $currency = $currencyRepository->createNew();
            $currency->setCode($code);
            $currency->setExchangeRate(1);

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

        do {
            if ($input->getOption('no-interaction')) {
                $countries = array('US');
            } else {
                $output->writeln('Please enter list of country codes, separated by commas or just hit ENTER to use "US". For example "US, PL, DE".');
                $codes = $this->ask($output, '<question>To which countries you are going to sell your goods?</question> ', array(), 'US');
                $countries = explode(',', $codes);
            }
            
            $valid = true;

            foreach ($countries as $code) {
                if (0 !== count($errors = $this->validate(trim($code), array(new Country())))) {
                    $valid = false;
                }

                $this->writeErrors($output, $errors);
            }
        } while (!$valid);

        foreach ($countries as $key => $code) {
            $code = trim($code);
            $name = Intl::getRegionBundle()->getCountryName($code);

            $output->writeln(sprintf('Adding <info>%s</info>.', $name));

            if (null !== $countryRepository->findOneByIsoName($code)) {
                continue;
            }

            $country = $countryRepository->createNew();
            $country->setName($name);
            $country->setIsoName($code);

            $countryManager->persist($country);
        }

        $countryManager->flush();
    }
}
