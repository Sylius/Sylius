<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Component\Seo\Model\Twitter;

/**
 * {@inheritdoc}
 *
 * @author Kamil Kokot <kamil.kokot@lakion.com>
 */
class AppCard extends AbstractCard implements AppCardInterface
{
    /**
     * {@inheritdoc}
     */
    protected $type = 'app';

    /**
     * @var string
     */
    protected $description;

    /**
     * @var string
     */
    protected $appNameIphone;

    /**
     * @var string
     */
    protected $appIdIphone;

    /**
     * @var string
     */
    protected $appUrlIphone;

    /**
     * @var string
     */
    protected $appNameIpad;

    /**
     * @var string
     */
    protected $appIdIpad;

    /**
     * @var string
     */
    protected $appUrlIpad;

    /**
     * @var string
     */
    protected $appNameGooglePlay;

    /**
     * @var string
     */
    protected $appIdGooglePlay;

    /**
     * @var string
     */
    protected $appUrlGooglePlay;

    /**
     * {@inheritdoc}
     */
    public function serialize()
    {
        return json_encode([
            $this->site,
            $this->siteId,
            $this->description,
            $this->appNameIphone,
            $this->appIdIphone,
            $this->appUrlIphone,
            $this->appNameIpad,
            $this->appIdIpad,
            $this->appUrlIpad,
            $this->appNameGooglePlay,
            $this->appIdGooglePlay,
            $this->appUrlGooglePlay,
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function unserialize($serialized)
    {
        list(
            $this->site,
            $this->siteId,
            $this->description,
            $this->appNameIphone,
            $this->appIdIphone,
            $this->appUrlIphone,
            $this->appNameIpad,
            $this->appIdIpad,
            $this->appUrlIpad,
            $this->appNameGooglePlay,
            $this->appIdGooglePlay,
            $this->appUrlGooglePlay,
        ) = json_decode($serialized, true);
    }
    
    /**
     * {@inheritdoc}
     */
    public function setDescription($description)
    {
        $this->description = $description;
    }
    
    /**
     * {@inheritdoc}
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * {@inheritdoc}
     */
    public function setAppNameIphone($appNameIphone)
    {
        $this->appNameIphone = $appNameIphone;
    }

    /**
     * {@inheritdoc}
     */
    public function getAppNameIphone()
    {
        return $this->appNameIphone;
    }

    /**
     * {@inheritdoc}
     */
    public function setAppIdIphone($appIdIphone)
    {
        $this->appIdIphone = $appIdIphone;
    }

    /**
     * {@inheritdoc}
     */
    public function getAppIdIphone()
    {
        return $this->appIdIphone;
    }

    /**
     * {@inheritdoc}
     */
    public function setAppUrlIphone($appUrlIphone)
    {
        $this->appUrlIphone = $appUrlIphone;
    }

    /**
     * {@inheritdoc}
     */
    public function getAppUrlIphone()
    {
        return $this->appUrlIphone;
    }

    /**
     * {@inheritdoc}
     */
    public function setAppNameIpad($appNameIpad)
    {
        $this->appNameIpad = $appNameIpad;
    }

    /**
     * {@inheritdoc}
     */
    public function getAppNameIpad()
    {
        return $this->appNameIpad;
    }

    /**
     * {@inheritdoc}
     */
    public function setAppIdIpad($appIdIpad)
    {
        $this->appIdIpad = $appIdIpad;
    }

    /**
     * {@inheritdoc}
     */
    public function getAppIdIpad()
    {
        return $this->appIdIpad;
    }

    /**
     * {@inheritdoc}
     */
    public function setAppUrlIpad($appUrlIpad)
    {
        $this->appUrlIpad = $appUrlIpad;
    }

    /**
     * {@inheritdoc}
     */
    public function getAppUrlIpad()
    {
        return $this->appUrlIpad;
    }

    /**
     * {@inheritdoc}
     */
    public function setAppNameGooglePlay($appNameGooglePlay)
    {
        $this->appNameGooglePlay = $appNameGooglePlay;
    }

    /**
     * {@inheritdoc}
     */
    public function getAppNameGooglePlay()
    {
        return $this->appNameGooglePlay;
    }

    /**
     * {@inheritdoc}
     */
    public function setAppIdGooglePlay($appIdGooglePlay)
    {
        $this->appIdGooglePlay = $appIdGooglePlay;
    }

    /**
     * {@inheritdoc}
     */
    public function getAppIdGooglePlay()
    {
        return $this->appIdGooglePlay;
    }

    /**
     * {@inheritdoc}
     */
    public function setAppUrlGooglePlay($appUrlGooglePlay)
    {
        $this->appUrlGooglePlay = $appUrlGooglePlay;
    }

    /**
     * {@inheritdoc}
     */
    public function getAppUrlGooglePlay()
    {
        return $this->appUrlGooglePlay;
    }
}