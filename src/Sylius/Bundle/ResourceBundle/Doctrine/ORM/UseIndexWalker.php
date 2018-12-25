<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Sylius\Bundle\ResourceBundle\Doctrine\ORM;

use Doctrine\ORM\Query\SqlWalker;

class UseIndexWalker extends SqlWalker
{
    public const HINT_USE_INDEX = 'UseIndexWalker.UseIndex';

    public function walkFromClause($fromClause)
    {
        $result = parent::walkFromClause($fromClause);
        if ($index = $this->getQuery()->getHint(self::HINT_USE_INDEX)) {
            $result = preg_replace('#(\bFROM\s*\w+\s*\w+)#', '\1 USE INDEX (' . $index . ')', $result);
        }

        return $result;
    }
}
