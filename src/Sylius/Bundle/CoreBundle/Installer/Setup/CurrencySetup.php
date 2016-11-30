<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\CoreBundle\Installer\Setup;

use Sylius\Component\Currency\Model\CurrencyInterface;
use Sylius\Component\Resource\Factory\FactoryInterface;
use Sylius\Component\Resource\Repository\RepositoryInterface;
use Symfony\Component\Console\Helper\QuestionHelper;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\Question;
use Symfony\Component\Intl\Intl;

/**
 * @author Mateusz Zalewski <mateusz.zalewski@lakion.com>
 */
final class CurrencySetup implements CurrencySetupInterface
{
    /**
     * @var RepositoryInterface
     */
    private $currencyRepository;

    /**
     * @var FactoryInterface
     */
    private $currencyFactory;

    /**
     * @param RepositoryInterface $currencyRepository
     * @param FactoryInterface $currencyFactory
     */
    public function __construct(RepositoryInterface $currencyRepository, FactoryInterface $currencyFactory)
    {
        $this->currencyRepository = $currencyRepository;
        $this->currencyFactory = $currencyFactory;
    }

    /**
     * {@inheritdoc}
     */
    public function setup(InputInterface $input, OutputInterface $output, QuestionHelper $questionHelper)
    {
        $code = $this->getCurrencyCodeFromUser($input, $output, $questionHelper);

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

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @param QuestionHelper $questionHelper
     *
     * @return string
     */
    private function getCurrencyCodeFromUser(InputInterface $input, OutputInterface $output, QuestionHelper $questionHelper)
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

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @param QuestionHelper $questionHelper
     *
     * @return string
     */
    private function getNewCurrencyCode(InputInterface $input, OutputInterface $output, QuestionHelper $questionHelper)
    {
        $question = new Question('Currency (press enter to use USD): ', 'USD');

        return trim($questionHelper->ask($input, $output, $question));
    }

    /**
     * @param string $code
     *
     * @return string|null
     */
    private function getCurrencyName($code)
    {
        return Intl::getCurrencyBundle()->getCurrencyName($code);
    }
}
