<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Component\Addressing\Model;

use Gedmo\Loggable\Entity\MappedSuperclass\AbstractLogEntry;
use Sylius\Component\Resource\Model\ResourceInterface;

/**
 * @author Mateusz Zalewski <mateusz.zalewski@lakion.com>
 */
class AddressLogEntry extends AbstractLogEntry implements ResourceInterface
{
    /**
     * @var string
     */
    protected $id;

    /**
     * {@inheritdoc}
     */
    public function getId()
    {
        return $this->id;
    }
}
