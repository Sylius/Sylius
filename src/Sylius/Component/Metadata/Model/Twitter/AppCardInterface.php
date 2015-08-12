<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Component\Metadata\Model\Twitter;

/**
 * @see https://dev.twitter.com/cards/types/app
 *
 * @author Kamil Kokot <kamil.kokot@lakion.com>
 */
interface AppCardInterface extends CardInterface
{
    /**
     * @param string $description
     */
    public function setDescription($description);

    /**
     * @return string
     */
    public function getDescription();

    /**
     * @param string $appNameIphone
     */
    public function setAppNameIphone($appNameIphone);

    /**
     * @return string
     */
    public function getAppNameIphone();

    /**
     * @param string $appIdIphone
     */
    public function setAppIdIphone($appIdIphone);

    /**
     * @return string
     */
    public function getAppIdIphone();

    /**
     * @param string $appUrlIphone
     */
    public function setAppUrlIphone($appUrlIphone);

    /**
     * @return string
     */
    public function getAppUrlIphone();

    /**
     * @param string $appNameIpad
     */
    public function setAppNameIpad($appNameIpad);

    /**
     * @return string
     */
    public function getAppNameIpad();

    /**
     * @param string $appIdIpad
     */
    public function setAppIdIpad($appIdIpad);

    /**
     * @return string
     */
    public function getAppIdIpad();

    /**
     * @param string $appUrlIpad
     */
    public function setAppUrlIpad($appUrlIpad);

    /**
     * @return string
     */
    public function getAppUrlIpad();

    /**
     * @param string $appNameGooglePlay
     */
    public function setAppNameGooglePlay($appNameGooglePlay);

    /**
     * @return string
     */
    public function getAppNameGooglePlay();

    /**
     * @param string $appIdGooglePlay
     */
    public function setAppIdGooglePlay($appIdGooglePlay);

    /**
     * @return string
     */
    public function getAppIdGooglePlay();

    /**
     * @param string $appUrlGooglePlay
     */
    public function setAppUrlGooglePlay($appUrlGooglePlay);

    /**
     * @return string
     */
    public function getAppUrlGooglePlay();
}
