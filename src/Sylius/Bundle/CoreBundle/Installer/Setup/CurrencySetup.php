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

namespace Sylius\Bundle\CoreBundle\Installer\Setup;

use Sylius\Component\Currency\Model\CurrencyInterface;
use Sylius\Component\Resource\Factory\FactoryInterface;
use Sylius\Component\Resource\Repository\RepositoryInterface;
use Symfony\Component\Console\Helper\QuestionHelper;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\Question;
use Symfony\Component\Intl\Intl;

final class CurrencySetup implements CurrencySetupInterface
{
    /** @var RepositoryInterface */
    private $currencyRepository;

    /** @var FactoryInterface */
    private $currencyFactory;

    public function __construct(RepositoryInterface $currencyRepository, FactoryInterface $currencyFactory)
    {
        $this->currencyRepository = $currencyRepository;
        $this->currencyFactory = $currencyFactory;
    }

    /**
     * {@inheritdoc}
     */
    public function setup(InputInterface $input, OutputInterface $output, QuestionHelper $questionHelper): CurrencyInterface
    {
        $code = $this->getCurrencyCodeFromUser($input, $output, $questionHelper);

        /** @var CurrencyInterface $existingCurrency */
        $existingCurrency = $this->currencyRepository->findOneBy(['code' => $code]);
        if (null !== $existingCurrency) {
            return $existingCurrency;
        }

        /** @var CurrencyInterface $currency */
        $currency = $this->currencyFactory->createNew();
        $currency->setCode($code);

        $this->currencyRepository->add($currency);

        return $currency;
    }

    private function getCurrencyCodeFromUser(InputInterface $input, OutputInterface $output, QuestionHelper $questionHelper): string
    {
        $code = $this->getNewCurrencyCode($input, $output, $questionHelper);
        $name = $this->getCurrencyName($code);

        while (null === $name) {
            $output->writeln(
                sprintf('<comment>Currency with code <info>%s</info> could not be resolved.</comment>', $code)
            );

            $code = $this->getNewCurrencyCode($input, $output, $questionHelper);
            $name = $this->getCurrencyName($code);
        }

        $output->writeln(sprintf('Adding <info>%s</info> currency.', $name));

        return $code;
    }

    private function getNewCurrencyCode(InputInterface $input, OutputInterface $output, QuestionHelper $questionHelper): string
    {
        $question = new Question('Currency (press enter to use USD): ', 'USD');

        return trim($questionHelper->ask($input, $output, $question));
    }

    private function getCurrencyName(string $code): ?string
    {
        return Intl::getCurrencyBundle()->getCurrencyName($code);
    }
}
