<?php

namespace Sylius\Bundle\CoreBundle\DataFetcher;

use Sylius\Bundle\CoreBundle\Doctrine\ORM\UserRepository;
use Sylius\Bundle\ReportBundle\DataFetcher\TimePeriod;
use Sylius\Component\Report\DataFetcher\DefaultDataFetchers;

/**
 * User registration data fetcher
 *
 * @author Łukasz Chruściel <lukasz.chrusciel@lakion.com>
 */
class UserRegistrationDataFetcher extends TimePeriod
{
    /**
     * @var UserRepository
     */
    private $userRepository;

    public function __construct(UserRepository $userRepository)
    {
        $this->userRepository = $userRepository;
    }

    /**
     * {@inheritdoc}
     */
    protected function getData(array $configuration = array())
    {
        return $this->userRepository->getRegistrationStatistic($configuration);
    }

    /**
     * {@inheritdoc}
     */
    public function getType()
    {
        return DefaultDataFetchers::USER_REGISTRATION;
    }
}
