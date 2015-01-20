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
        $fetched = array();
        $fetched['column_name'] = array('month','user_total');
        $data = $this->userRepository->findByMonth();
        foreach ($data as $row) {
            $fetched[$row['month']] = $row['user_total'];
        }
        return $fetched;
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