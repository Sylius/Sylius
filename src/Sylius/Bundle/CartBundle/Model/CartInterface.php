<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\CartBundle\Model;

/**
 * Cart model interface.
 *
 * @author Paweł Jędrzejewski <pjedrzejewski@diweb.pl>
 */
interface CartInterface
{
    function getTotalItems();
    function setTotalItems($totalItems);
    function incrementTotalItems($amount = 1);
    function isLocked();
    function setLocked($locked);
    function isEmpty();
    function countItems();
    function clearItems();
    function setItems($items);
    function getItems();
    function addItem(ItemInterface $item);
    function removeItem(ItemInterface $item);
    function hasItem(ItemInterface $item);
    function getExpiresAt();
    function isExpired();
    function setExpiresAt(\DateTime $expiresAt);
    function incrementExpiresAt();
}