<?php
/**
 * Created by PhpStorm.
 * User: mamazu
 * Date: 05/02/18
 * Time: 11:01
 */

declare(strict_types=1);

namespace Sylius\Bundle\AddressingBundle\Doctrine\ORM;


use Sylius\Bundle\ResourceBundle\Doctrine\ORM\EntityRepository;
use Sylius\Component\Addressing\Model\PostalCodeInterface;
use Sylius\Component\Addressing\Repository\PostCodeRepositoryInterface;

class PostCodeRepository extends EntityRepository implements PostCodeRepositoryInterface
{

    public function findOneBy(array $criteria, array $orderBy = null): ?PostalCodeInterface
    {
        $code = 0;

        // Remove the code from the array to do a normal query
        if (in_array('code', array_keys($criteria))) {
            $code = (string)$criteria['code'];
            unset($criteria['code']);
        }

        $result = parent::findBy($criteria, $orderBy);

        // If the code is a string, filter through the results with the code
        if (is_string($code)) {
            $possibleResults = array_filter($result, function (PostalCodeInterface $resultElement) use ($code) {
                return $resultElement->getCode() === $code;
            });
            return count($possibleResults) > 0 ? reset($possibleResults) : null;
        }

        if(count($result) === 0){
            return null;
        }

        return reset($result);
    }

    /**
     *
     * @param string $code
     *
     * @return null|PostalCodeInterface
     */
    public function findOneByCode(string $code): ?PostalCodeInterface
    {
        return $this->findOneBy(['code' => $code]);
    }
}