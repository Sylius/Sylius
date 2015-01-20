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
    public function fetch($config){
        $queryBuilder = $this->userRepository->createQueryBuilder('ur');
        $queryBuilder
            ->where("ur.createdAt > '2015-01-10 00:00:00'")
            ->andWhere("ur.createdAt < '2015-01-17 00:00:00'")
        ;

        $result = $queryBuilder
            ->getQuery()
            ->getArrayResult()
        ;

        return $result;
    }

    /**
     * {@inheritdoc}
     */
    public function getType(){
        return 'user_registration';
    }
}