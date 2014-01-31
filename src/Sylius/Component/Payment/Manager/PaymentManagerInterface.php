<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Component\Payment\Manager;

use Sylius\Component\Payment\Model\PaymentsSubjectInterface;

interface PaymentManagerInterface
{
    /**
     * Initialize payment gateway & return it's redirect URL.
     *
     * @param object $object
     * @param array  $callbackDetails
     *
     * @return string
     *
     * @throws \InvalidArgumentException
     */
    public function initialize($object, array $callbackDetails);

    /**
     * Handle payment data.
     *
     * @param mixed $data
     *
     * @return string
     *
     * @throws \InvalidArgumentException
     */
    public function handle($data);

    /**
     * Validate that given object is supported by this manager.
     *
     * @param object $object
     *
     * @throws \InvalidArgumentException
     */
    public function supports($object);

    /**
     * Return object instance.
     *
     * @return PaymentsSubjectInterface
     */
    public function getSubject();
}
