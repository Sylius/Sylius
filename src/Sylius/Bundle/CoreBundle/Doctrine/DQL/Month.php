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

namespace Sylius\Bundle\CoreBundle\Doctrine\DQL;

use Doctrine\ORM\Query\AST\Functions\FunctionNode;
use Doctrine\ORM\Query\AST\Node;
use Doctrine\ORM\Query\Lexer;
use Doctrine\ORM\Query\Parser;
use Doctrine\ORM\Query\SqlWalker;

final class Month extends FunctionNode
{
    /** @var Node|string|null */
    public $date;

    public function parse(Parser $parser): void
    {
        $parser->match(Lexer::T_IDENTIFIER);
        $parser->match(Lexer::T_OPEN_PARENTHESIS);

        $this->date = $parser->ArithmeticPrimary();

        $parser->match(Lexer::T_CLOSE_PARENTHESIS);
    }

    public function getSql(SqlWalker $sqlWalker): string
    {
        $platformName = $sqlWalker->getConnection()->getDatabasePlatform()->getName();

        switch ($platformName) {
            case 'mysql':
                return sprintf('MONTH(%s)', $sqlWalker->walkArithmeticPrimary($this->date));
            case 'postgresql':
                return sprintf('EXTRACT(MONTH FROM %s)', $sqlWalker->walkArithmeticPrimary($this->date));
        }

        throw new \RuntimeException(sprintf('Platform "%s" is not supported!', $platformName));
    }
}
