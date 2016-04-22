<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\CoreBundle\DataFetcher;

use Sylius\Bundle\ReportBundle\DataFetcher\TimePeriod;
use Sylius\Bundle\UserBundle\Doctrine\ORM\UserRepository;
use Sylius\Component\Report\DataFetcher\DefaultDataFetchers;

/**
 * @author Łukasz Chruściel <lukasz.chrusciel@lakion.com>
 */
class UserRegistrationDataFetcher extends TimePeriod
{
    /**
     * @var UserRepository
     */
    private $userRepository;

    /**
     * @param UserRepository $userRepository
     */
    public function __construct(UserRepository $userRepository)
    {
        $this->userRepository = $userRepository;
    }

    /**
     * {@inheritdoc}
     */
    protected function getData(array $configuration = [])
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
