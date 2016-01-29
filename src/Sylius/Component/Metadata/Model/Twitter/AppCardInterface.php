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
     * The twitter:description property.
     *
     * @return string
     */
    public function getDescription();

    /**
     * The twitter:description property.
     *
     * @param string $description
     */
    public function setDescription($description);

    /**
     * The twitter:app:name:iphone property.
     *
     * @return string
     */
    public function getAppNameIphone();

    /**
     * The twitter:app:name:iphone property.
     *
     * @param string $appNameIphone
     */
    public function setAppNameIphone($appNameIphone);

    /**
     * The twitter:app:id:iphone property.
     *
     * @return string
     */
    public function getAppIdIphone();

    /**
     * The twitter:app:id:iphone property.
     *
     * @param string $appIdIphone
     */
    public function setAppIdIphone($appIdIphone);

    /**
     * The twitter:app:url:iphone property.
     *
     * @return string
     */
    public function getAppUrlIphone();

    /**
     * The twitter:app:url:iphone property.
     *
     * @param string $appUrlIphone
     */
    public function setAppUrlIphone($appUrlIphone);

    /**
     * The twitter:app:name:ipad property.
     *
     * @return string
     */
    public function getAppNameIpad();

    /**
     * The twitter:app:name:ipad property.
     *
     * @param string $appNameIpad
     */
    public function setAppNameIpad($appNameIpad);

    /**
     * The twitter:app:id:ipad property.
     *
     * @return string
     */
    public function getAppIdIpad();

    /**
     * The twitter:app:id:ipad property.
     *
     * @param string $appIdIpad
     */
    public function setAppIdIpad($appIdIpad);

    /**
     * The twitter:app:url:ipad property.
     *
     * @return string
     */
    public function getAppUrlIpad();

    /**
     * The twitter:app:url:ipad property.
     *
     * @param string $appUrlIpad
     */
    public function setAppUrlIpad($appUrlIpad);

    /**
     * The twitter:app:name:googleplay property.
     *
     * @return string
     */
    public function getAppNameGooglePlay();

    /**
     * The twitter:app:name:googleplay property.
     *
     * @param string $appNameGooglePlay
     */
    public function setAppNameGooglePlay($appNameGooglePlay);

    /**
     * The twitter:app:id:googleplay property.
     *
     * @return string
     */
    public function getAppIdGooglePlay();

    /**
     * The twitter:app:id:googleplay property.
     *
     * @param string $appIdGooglePlay
     */
    public function setAppIdGooglePlay($appIdGooglePlay);

    /**
     * The twitter:app:url:googleplay property.
     *
     * @return string
     */
    public function getAppUrlGooglePlay();

    /**
     * The twitter:app:url:googleplay property.
     *
     * @param string $appUrlGooglePlay
     */
    public function setAppUrlGooglePlay($appUrlGooglePlay);
}
