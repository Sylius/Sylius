<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\SalesBundle\Model;

/**
 * Order interface.
 *
 * @author Paweł Jędrzejewski <pjedrzejewski@diweb.pl>
 */
interface OrderInterface
{
    function getId();
    function isConfirmed();
    function setConfirmed($confirmed);
    function generateConfirmationToken();
    function getConfirmationToken();
    function setConfirmationToken($confirmationToken);
    function isClosed();
    function setClosed($closed);
    function getStatus();
    function setStatus(StatusInterface $status);

    /**
     * Get creation time.
     *
     * @return \DateTime
     */
    function getCreatedAt();

    /**
     * Increments creation time.
     *
     * @return null
     */
    function incrementCreatedAt();

    /**
     * Get modification time.
     *
     * @return \DateTime
     */
    function getUpdatedAt();

    /**
     * Increments modification time.
     *
     * @return null
     */
    function incrementUpdatedAt();
}
