<?php

namespace Smile\Component\Store\Model;


use Smile\Component\Scope\Entity\ScopeTrait;

class Store implements StoreInterface
{
    use ScopeTrait;

    /**
     * @var mixed
     */
    protected $id;
    /**
     * @var string
     */
    protected $url;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->createdAt = new \DateTime();
    }

    public function getId()
    {
        return $this->id;
    }

    /**
     * {@inheritdoc}
     */
    public function getUrl()
    {
        return $this->url;
    }

    /**
     * {@inheritdoc}
     */
    public function setUrl($url)
    {
        $this->url = $url;

        return $this;
    }
}