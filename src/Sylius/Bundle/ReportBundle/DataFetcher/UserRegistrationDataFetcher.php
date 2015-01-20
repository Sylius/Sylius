<?php

namespace Sylius\Bundle\ReportBundle\DataFetcher;

use Sylius\Bundle\CoreBundle\Doctrine\ORM\UserRepository;
use Sylius\Component\Report\DataFetcher\DataFetcherInterface;

/**
* User registration data fetcher
* 
* @author Łukasz Chruściel <lukasz.chrusciel@lakion.com>
*/
class UserRegistrationDataFetcher implements DataFetcherInterface
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
    public function fetch(array $configuration){
        return $this->userRepository->findByMonth();
    }

    /**
     * {@inheritdoc}
     */
    public function getType(){
        return 'user_registration';
    }

    public static function getPeriodChoices()
    {
        return array('day', 'week', 'month', 'year');
    }
}