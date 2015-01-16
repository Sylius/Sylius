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
use Symfony\Component\Intl\Intl;

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
        $output->writeln('You can now load sample data or configure the store yourself.');

        $this->setupLocales($input, $output);
        $this->setupCurrencies($input, $output);
        $this->setupCountries($input, $output);
        $this->setupAdministratorUser($input, $output);

        return $this;
    }

    protected function setupAdministratorUser(InputInterface $input, OutputInterface $output)
    {
        $output->writeln('Create your administrator account.');

        $user = $this->get('sylius.repository.user')->createNew();

        $user->setFirstname($this->askRequired($output, '<question>Your firstname:</question> '));
        $user->setLastname($this->askRequired($output, '<question>Your lastname:</question> '));
        $user->setEmail($this->askRequired($output, '<question>Your e-mail:</question> '));
        $user->setPlainPassword($this->askRequired($output, '<question>Your password:</question> '));
        $user->setEnabled(true);
        $user->setRoles(array('ROLE_SYLIUS_ADMIN'));

        $userManager = $this->get('sylius.manager.user');

        $userManager->persist($user);
        $userManager->flush();
    }

    protected function setupLocales(InputInterface $input, OutputInterface $output)
    {
        $dialog = $this->getHelperSet()->get('dialog');

        $localeRepository = $this->get('sylius.repository.locale');
        $localeManager = $this->get('sylius.manager.locale');

        $output->writeln('Please enter list of locale codes, separated by commas or just hit ENTER to use "en_US". For example "en_US, de_DE".');
        $codes = $dialog->ask($output, '<question>In which language your customers can browse the store?</question> ', 'en_US');

        $locales = explode(',', $codes);

        foreach ($locales as $key => $code) {
            $code = trim($code);

            $locale = $localeRepository->createNew();
            $locale->setCode($code);

            $displayName = \Locale::getDisplayName($code);
            $output->writeln(sprintf('Adding <info>%s</info>.', $displayName));

            $localeManager->persist($locale);
        }

        $localeManager->flush();

        return $this;
    }

    protected function setupCurrencies(InputInterface $input, OutputInterface $output)
    {
        $dialog = $this->getHelperSet()->get('dialog');

        $currencyRepository = $this->get('sylius.repository.currency');
        $currencyManager = $this->get('sylius.manager.currency');

        $output->writeln('Please enter list of currency codes, separated by commas or just hit ENTER to use "USD". For example "USD, EUR, GBP".');
        $codes = $dialog->ask($output, '<question>In which currency your customers can buy goods?</question> ', 'USD');

        $currencies = explode(',', $codes);

        foreach ($currencies as $key => $code) {
            $code = trim($code);

            $currency = $currencyRepository->createNew();
            $currency->setCode($code);
            $currency->setExchangeRate(1);

            $displayName = Intl::getCurrencyBundle()->getCurrencyName($code);
            $output->writeln(sprintf('Adding <info>%s</info>.', $displayName));

            $currencyManager->persist($currency);
        }

        $output->writeln('Remember to update (manually or automatically) the exchange rates in the administration panel!');

        $currencyManager->flush();
        $output->writeln('');

        return $this;
    }

    protected function setupCountries(InputInterface $input, OutputInterface $output)
    {
        $dialog = $this->getHelperSet()->get('dialog');

        $countryRepository = $this->get('sylius.repository.country');
        $countryManager = $this->get('sylius.manager.country');

        $output->writeln('Please enter list of country codes, separated by commas or just hit ENTER to use "US". For example "US, PL, DE".');
        $codes = $dialog->ask($output, '<question>To which countries you are going to sell your goods?</question> ', 'US');

        $countries = explode(',', $codes);

        foreach ($countries as $key => $code) {
            $code = trim($code);
            $name = Intl::getRegionBundle()->getCountryName($code);

            $country = $countryRepository->createNew();
            $country->setName($name);
            $country->setIsoName($code);

            $output->writeln(sprintf('Adding <info>%s</info>.', $name));

            $countryManager->persist($country);
        }

        $countryManager->flush();

        return $this;
    }
}
