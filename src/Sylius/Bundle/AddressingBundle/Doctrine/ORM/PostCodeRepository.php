<?php

declare(strict_types=1);

namespace Sylius\Bundle\AddressingBundle\Doctrine\ORM;

use Sylius\Bundle\ResourceBundle\Doctrine\ORM\EntityRepository;
use Sylius\Component\Addressing\Model\PostCodeInterface;
use Sylius\Component\Addressing\Repository\PostCodeRepositoryInterface;

final class PostCodeRepository extends EntityRepository implements PostCodeRepositoryInterface
{
    public function findOneBy(array $criteria, array $orderBy = null): ?PostCodeInterface
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
            $possibleResults = array_filter($result, function (PostCodeInterface $resultElement) use ($code) {
                return $resultElement->getCode() === $code;
            });
            return count($possibleResults) > 0 ? reset($possibleResults) : null;
        }

        // Else proceed as normal but just return one
        return count($result) === 0 ? null : reset($result);
    }

    /**
     *
     * @param string $code
     *
     * @return null|PostCodeInterface
     */
    public function findOneByCode(string $code): ?PostCodeInterface
    {
        return $this->findOneBy(['code' => $code]);
    }
}
