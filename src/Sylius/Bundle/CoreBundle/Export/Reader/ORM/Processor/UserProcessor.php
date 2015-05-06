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
    private static $USER_DATETIME_KEYS = array(
        'lastLogin',
        'passwordRequestedAt',
        'expiresAt',
        'credentialsExpireAt',
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
        foreach ($this::$USER_DATETIME_KEYS as $key) {
            $user[$key] = $this->dateConverter->toString($user[$key], $format);
        }

        return $user;
    }
}
