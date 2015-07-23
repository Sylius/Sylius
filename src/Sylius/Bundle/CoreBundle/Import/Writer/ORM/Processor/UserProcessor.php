<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\CoreBundle\Import\Writer\ORM\Processor;

use Sylius\Component\ImportExport\Converter\DateConverterInterface;

/**
 * @author Łukasz Chruściel <lukasz.chrusciel@lakion.com>
 */
class UserProcessor implements UserProcessorInterface
{
    /**
     * @var DateConverterInterface
     */
    private $dateConverter;

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
     * @param DateConverterInterface $dateConverter
     */
    public function __construct(DateConverterInterface $dateConverter)
    {
        $this->dateConverter = $dateConverter;
    }

    /**
     * {@inheritdoc}
     */
    public function convert(array $users, $format)
    {
        foreach ($users as $key => $user) {
            $user = $this->convertDates($user, $format);
            $user = $this->restoreCustomerArray($user);
            $user['roles'] = json_decode($user['roles']);
            $users[$key] = $user;
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
            if (!empty($user[$key])) {
                $user[$key] = $this->dateConverter->toDateTime($user[$key], $format);
            }
        }

        return $user;
    }

    /**
     * @param array $user
     *
     * @return array
     */
    private function restoreCustomerArray(array $user)
    {
        $customersKeys = array_filter(array_keys($user), function($key) {
            return false !== strpos($key, 'customer');
        });

        $user['customer'] = array();

        foreach ($customersKeys as $key) {
            $newKey = lcfirst(str_replace('customer', '', $key));
            $user['customer'][$newKey] = $user[$key];
        }

        foreach ($customersKeys as $unnecessaryKey) {
            unset($user[$unnecessaryKey]);
        }

        return $user;
    }
}
