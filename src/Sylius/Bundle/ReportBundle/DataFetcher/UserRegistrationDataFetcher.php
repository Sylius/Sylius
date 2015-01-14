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
        $queryBuilder = $this->userRepository->getEntityMenager->createQueryBuilder('user_registration');
        $queryBuilder
            ->select('*')
            ->from('sylius_user', 'su')
            ->where('dayofyear(created_at) > :start AND dayofyear(created_at) < :end')
            ->setParameters(array("start" => '10', "end" => '17'));
        ;

        $result = $queryBuilder
            ->getQuery()
            ->getArrayResult()
        ;

        var_dump($result);
        exit;

        return $result;
    }

    /**
     * {@inheritdoc}
     */
    public function getType(){
        return 'user_registration';
    }
}