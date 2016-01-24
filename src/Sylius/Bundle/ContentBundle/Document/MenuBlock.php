<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\ContentBundle\Document;

use Sylius\Component\Resource\Model\ResourceInterface;
use Symfony\Cmf\Bundle\BlockBundle\Doctrine\Phpcr\MenuBlock as BaseMenuBlock;

/**
 * @author Magdalena Banasiak <magdalena.banasiak@lakion.com>
 */
class MenuBlock extends BaseMenuBlock implements ResourceInterface
{
}
