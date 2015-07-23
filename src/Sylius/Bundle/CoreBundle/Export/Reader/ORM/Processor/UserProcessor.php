<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\CoreBundle\Export\Reader\ORM\Processor;

use Sylius\Component\ImportExport\Converter\DateConverter;

class UserProcessor implements UserProcessorInterface
{
    /**
     * @var array
     */
    private static $userDateTimeKeys = array(
        'lastLogin',
        'passwordRequestedAt',
        'expiresAt',
        'credentialsExpireAt',
        'customerCreatedAt',
        'customerBirthday',
        'customerUpdatedAt',
        'customerDeletedAt',
        'createdAt',
        'updatedAt',
        'deletedAt',
    );

    /**
     * @var DateConverter
     */
    private $dateConverter;

    /**
     * @param DateConverter $dateConverter
     */
    public function __construct(DateConverter $dateConverter)
    {
        $this->dateConverter = $dateConverter;
    }

    /**
     * @param array  $users
     * @param string $format
     *
     * @return array
     */
    public function convert(array $users, $format)
    {
        foreach ($users as $key => $user) {
            $user = array_merge($users[$key], $this->addCustomerPrefix($users[$key]['customer']));
            unset($user['customer']);
            $users[$key] = $this->convertDates($user, $format);
            $users[$key]['roles'] = json_encode($user['roles']);
        }

        return $users;
    }

    /**
     * @param array  $user
     * @param string $format
     *
     * @return array
     */
    private function convertDates(array $user, $format)
    {
        foreach ($this::$userDateTimeKeys as $key) {
            if (null !== $user[$key]) {
                $user[$key] = $this->dateConverter->toString($user[$key], $format);
            }
        }

        return $user;
    }

    /**
     * @param array $customer
     *
     * @return array
     */
    private function addCustomerPrefix(array $customer)
    {
        $resultArray = array();
        foreach ($customer as $key => $value) {
            $resultArray['customer'.ucfirst($key)] = $value;
        }

        return $resultArray;
    }
}
