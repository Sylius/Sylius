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

namespace Sylius\Bundle\ProductBundle\Doctrine\ORM\Query\AST\Function;

use Doctrine\DBAL\Exception;
use Doctrine\ORM\Query\AST\ASTException;
use Doctrine\ORM\Query\AST\Functions\FunctionNode;
use Doctrine\ORM\Query\AST\Literal;
use Doctrine\ORM\Query\AST\Node;
use Doctrine\ORM\Query\Lexer;
use Doctrine\ORM\Query\Parser;
use Doctrine\ORM\Query\QueryException;
use Doctrine\ORM\Query\SqlWalker;

/**
 * @see https://github.com/ScientaNL/DoctrineJsonFunctions/blob/master/src/Query/AST/Functions/AbstractJsonFunctionNode.php
 */
abstract class AbstractJsonFunctionNode extends FunctionNode
{
    /** @var string|null */
    public const FUNCTION_NAME = null;

    protected const ALPHA_NUMERIC = 'alphaNumeric';

    protected const STRING_PRIMARY_ARG = 'stringPrimary';

    protected const STRING_ARG = 'string';

    protected const VALUE_ARG = 'newValue';

    /** @var string[] */
    protected array $requiredArgumentTypes = [];

    /** @var string[] */
    protected array $optionalArgumentTypes = [];

    protected bool $allowOptionalArgumentRepeat = false;

    /** @var array<Node|null> */
    protected array $parsedArguments = [];

    /**
     * @throws QueryException
     */
    public function parse(Parser $parser): void
    {
        $parser->match(Lexer::T_IDENTIFIER);
        $parser->match(Lexer::T_OPEN_PARENTHESIS);

        $argumentParsed = $this->parseArguments($parser, $this->requiredArgumentTypes);

        if (!empty($this->optionalArgumentTypes)) {
            $this->parseOptionalArguments($parser, $argumentParsed);
        }

        $parser->match(Lexer::T_CLOSE_PARENTHESIS);
    }

    /**
     * @throws QueryException
     */
    protected function parseOptionalArguments(Parser $parser, bool $argumentParsed): void
    {
        $continueParsing = !$parser->getLexer()->isNextToken(Lexer::T_CLOSE_PARENTHESIS);
        while ($continueParsing) {
            $argumentParsed = $this->parseArguments($parser, $this->optionalArgumentTypes, $argumentParsed);
            $continueParsing = $this->allowOptionalArgumentRepeat && $parser->getLexer()->isNextToken(Lexer::T_COMMA);
        }
    }

    /**
     * @param string[] $argumentTypes
     *
     * @throws QueryException
     */
    protected function parseArguments(Parser $parser, array $argumentTypes, bool $argumentParsed = false): bool
    {
        foreach ($argumentTypes as $argType) {
            if ($argumentParsed) {
                $parser->match(Lexer::T_COMMA);
            } else {
                $argumentParsed = true;
            }

            $this->parsedArguments[] = match ($argType) {
                self::STRING_PRIMARY_ARG => $parser->StringPrimary(),
                self::STRING_ARG => $this->parseStringLiteral($parser),
                self::ALPHA_NUMERIC => $this->parseAlphaNumericLiteral($parser),
                self::VALUE_ARG => $parser->NewValue(),
                default => throw QueryException::semanticalError(sprintf('Unknown function argument type %s for %s()', $argType, static::FUNCTION_NAME)),
            };
        }

        return $argumentParsed;
    }

    /**
     * @throws QueryException
     */
    protected function parseStringLiteral(Parser $parser): Literal
    {
        $lexer = $parser->getLexer();
        $lookaheadType = $lexer->lookahead->type;

        if ($lookaheadType != Lexer::T_STRING) {
            $parser->syntaxError('string');
        }

        return $this->matchStringLiteral($parser, $lexer);
    }

    /**
     * @throws QueryException
     */
    protected function parseAlphaNumericLiteral(Parser $parser): Literal
    {
        $lexer = $parser->getLexer();
        $lookaheadType = $lexer->lookahead->type;

        switch ($lookaheadType) {
            case Lexer::T_STRING:
                return $this->matchStringLiteral($parser, $lexer);
            case Lexer::T_INTEGER:
            case Lexer::T_FLOAT:
                $parser->match(
                    $lexer->isNextToken(Lexer::T_INTEGER) ? Lexer::T_INTEGER : Lexer::T_FLOAT,
                );

                return new Literal(Literal::NUMERIC, $lexer->token->value);
            default:
                $parser->syntaxError('numeric');
        }
    }

    private function matchStringLiteral(Parser $parser, Lexer $lexer): Literal
    {
        $parser->match(Lexer::T_STRING);

        return new Literal(Literal::STRING, $lexer->token->value);
    }

    /**
     * @throws ASTException
     * @throws Exception
     */
    public function getSql(SqlWalker $sqlWalker): string
    {
        $this->validatePlatform($sqlWalker);

        $args = [];
        foreach ($this->parsedArguments as $parsedArgument) {
            if ($parsedArgument === null) {
                $args[] = 'NULL';
            } else {
                $args[] = $parsedArgument->dispatch($sqlWalker);
            }
        }

        return $this->getSqlForArgs($args);
    }

    /**
     * @param string[] $arguments
     */
    protected function getSqlForArgs(array $arguments): string
    {
        return sprintf('%s(%s)', $this->getSQLFunction(), implode(', ', $arguments));
    }

    protected function getSQLFunction(): string
    {
        return static::FUNCTION_NAME;
    }

    /**
     * @throws Exception
     */
    abstract protected function validatePlatform(SqlWalker $sqlWalker): void;
}
