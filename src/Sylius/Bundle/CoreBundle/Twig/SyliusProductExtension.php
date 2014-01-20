<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\CoreBundle\Twig;

use Sylius\Bundle\ResourceBundle\Doctrine\ORM\EntityRepository;

/**
 * Sylius product twig helper.
 *
 * @author Joseph Bielawski <stloyd@gmail.com>
 */
class SyliusProductExtension extends \Twig_Extension
{
    /**
     * @var EntityRepository
     */
    private $repository;

    /**
     * Constructor.
     *
     * @param EntityRepository $repository
     */
    public function __construct(EntityRepository $repository)
    {
        $this->repository = $repository;
    }

    /**
     * {@inheritdoc}
     */
    public function getFunctions()
    {
        return array(
             new \Twig_SimpleFunction('sylius_product_latest', array($this, 'fetchLatest')),
        );
    }

    /**
     * @param integer $limit
     *
     * @return array
     */
    public function fetchLatest($limit = 10)
    {
        return $this->repository->findLatest($limit);
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'sylius_product';
    }
}
