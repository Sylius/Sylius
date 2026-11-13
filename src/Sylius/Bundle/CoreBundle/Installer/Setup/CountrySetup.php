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

use Sylius\Component\Addressing\Model\CountryInterface;
use Sylius\Resource\Doctrine\Persistence\RepositoryInterface;
use Sylius\Resource\Factory\FactoryInterface;
use Symfony\Component\Console\Helper\QuestionHelper;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\Question;
use Symfony\Component\Intl\Countries;
use Symfony\Component\Intl\Exception\MissingResourceException;

final class CountrySetup implements CountrySetupInterface
{
    /**
     * @param FactoryInterface<CountryInterface> $countryFactory
     * @param RepositoryInterface<CountryInterface> $countryRepository
     */
    public function __construct(
        private readonly FactoryInterface $countryFactory,
        private readonly RepositoryInterface $countryRepository,
        private string $country = 'US',
    ) {
        $this->country = trim($country);
    }

    public function setup(InputInterface $input, OutputInterface $output, QuestionHelper $questionHelper): CountryInterface
    {
        $code = $this->getCountryCodeFromUser($input, $output, $questionHelper);

        /** @var CountryInterface|null $existingCountry */
        $existingCountry = $this->countryRepository->findOneBy(['code' => $code]);
        if (null !== $existingCountry) {
            return $existingCountry;
        }

        /** @var CountryInterface $country */
        $country = $this->countryFactory->createNew();
        $country->setCode($code);
        $country->setEnabled(true);

        $this->countryRepository->add($country);

        return $country;
    }

    private function getCountryCodeFromUser(InputInterface $input, OutputInterface $output, QuestionHelper $questionHelper): string
    {
        $code = $this->getNewCountryCode($input, $output, $questionHelper);
        $name = $this->getCountryName($code);

        while (null === $name) {
            $output->writeln(
                sprintf('<comment>Country with code <info>%s</info> could not be resolved.</comment>', $code),
            );

            $code = $this->getNewCountryCode($input, $output, $questionHelper);
            $name = $this->getCountryName($code);
        }

        $output->writeln(sprintf('Adding <info>%s</info> country.', $name));

        return $code;
    }

    private function getNewCountryCode(InputInterface $input, OutputInterface $output, QuestionHelper $questionHelper): string
    {
        $question = new Question(sprintf('Country (press enter to use %s): ', $this->country), $this->country);

        return strtoupper(trim($questionHelper->ask($input, $output, $question)));
    }

    private function getCountryName(string $code): ?string
    {
        try {
            return Countries::getName($code);
        } catch (MissingResourceException) {
            return null;
        }
    }
}
