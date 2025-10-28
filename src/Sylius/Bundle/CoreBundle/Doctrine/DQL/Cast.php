<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Sylius Sp. z o.o.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Sylius\Bundle\CoreBundle\Doctrine\DQL;

use Doctrine\DBAL\Platforms\MySQLPlatform;
use Doctrine\ORM\Query\AST\Functions\FunctionNode;
use Doctrine\ORM\Query\AST\Node;
use Doctrine\ORM\Query\Parser;
use Doctrine\ORM\Query\SqlWalker;
use Doctrine\ORM\Query\TokenType;

final class Cast extends FunctionNode
{
    public Node|string $value;

    public string $type;

    public function parse(Parser $parser): void
    {
        $parser->match(TokenType::T_IDENTIFIER);
        $parser->match(TokenType::T_OPEN_PARENTHESIS);

        $this->value = $parser->ArithmeticPrimary();

        $parser->match(TokenType::T_AS);

        $parser->match(TokenType::T_IDENTIFIER);
        $lexer = $parser->getLexer();
        $this->type = (string) $lexer->token->value;

        $parser->match(TokenType::T_CLOSE_PARENTHESIS);
    }

    public function getSql(SqlWalker $sqlWalker): string
    {
        $platform = $sqlWalker->getConnection()->getDatabasePlatform();

        $type = $this->type;

        if (is_a($platform, MySQLPlatform::class, true) && 'text' === $type) {
            $type = 'char';
        }

        return sprintf(
            'CAST(%s AS %s)',
            $sqlWalker->walkArithmeticPrimary($this->value),
            $type,
        );
    }
}
