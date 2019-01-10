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

namespace Sylius\Bundle\CoreBundle\Routing\Matcher\Dumper;

use Symfony\Component\ExpressionLanguage\ExpressionFunctionProviderInterface;
use Symfony\Component\ExpressionLanguage\ExpressionLanguage;
use Symfony\Component\Routing\Matcher\Dumper\MatcherDumper;
use Symfony\Component\Routing\Matcher\Dumper\StaticPrefixCollection;
use Symfony\Component\Routing\Matcher\RedirectableUrlMatcherInterface;
use Symfony\Component\Routing\Route;
use Symfony\Component\Routing\RouteCollection;

/**
 * This service is temporarily overwritten, due to problems with PhpMatcherDumper in Symfony 4.1.8 and 4.1.9
 * Should be removed after fixing it in Symfony 4.1 or bumping required Symfony version in Sylius to 4.2
 *
 * @internal
 */
final class PhpMatcherDumper extends MatcherDumper
{
    private $expressionLanguage;
    private $signalingException;

    /** @var ExpressionFunctionProviderInterface[] */
    private $expressionLanguageProviders = [];

    /**
     * Dumps a set of routes to a PHP class.
     *
     * Available options:
     *
     *  * class:      The class name
     *  * base_class: The base class name
     *
     * @param array $options An array of options
     *
     * @return string A PHP class representing the matcher class
     */
    public function dump(array $options = [])
    {
        $options = array_replace([
            'class' => 'ProjectUrlMatcher',
            'base_class' => 'Symfony\\Component\\Routing\\Matcher\\UrlMatcher',
        ], $options);

        // trailing slash support is only enabled if we know how to redirect the user
        $interfaces = class_implements($options['base_class']);
        $supportsRedirections = isset($interfaces[RedirectableUrlMatcherInterface::class]);

        return <<<EOF
<?php

use Symfony\Component\Routing\Exception\MethodNotAllowedException;
use Symfony\Component\Routing\Exception\ResourceNotFoundException;
use Symfony\Component\Routing\RequestContext;

/**
 * This class has been auto-generated
 * by the Symfony Routing Component.
 */
class {$options['class']} extends {$options['base_class']}
{
    public function __construct(RequestContext \$context)
    {
        \$this->context = \$context;
    }

{$this->generateMatchMethod($supportsRedirections)}
}

EOF;
    }

    public function addExpressionLanguageProvider(ExpressionFunctionProviderInterface $provider)
    {
        $this->expressionLanguageProviders[] = $provider;
    }

    /**
     * Generates the code for the match method implementing UrlMatcherInterface.
     */
    private function generateMatchMethod(bool $supportsRedirections): string
    {
        // Group hosts by same-suffix, re-order when possible
        $matchHost = false;
        $routes = new StaticPrefixCollection();
        foreach ($this->getRoutes()->all() as $name => $route) {
            if ($host = $route->getHost()) {
                $matchHost = true;
                $host = '/' . strtr(strrev($host), '}.{', '(/)');
            }

            $routes->addRoute($host ?: '/(.*)', [$name, $route]);
        }
        $routes = $matchHost ? $routes->populateCollection(new RouteCollection()) : $this->getRoutes();

        $code = rtrim($this->compileRoutes($routes, $matchHost), "\n");
        $fetchHost = $matchHost ? "        \$host = strtolower(\$context->getHost());\n" : '';

        $code = <<<EOF
    {
        \$allow = \$allowSchemes = array();
        \$pathinfo = rawurldecode(\$rawPathinfo);
        \$context = \$this->context;
        \$requestMethod = \$canonicalMethod = \$context->getMethod();
{$fetchHost}
        if ('HEAD' === \$requestMethod) {
            \$canonicalMethod = 'GET';
        }

$code

EOF;

        if ($supportsRedirections) {
            return <<<'EOF'
    public function match($pathinfo)
    {
        $allow = $allowSchemes = array();
        if ($ret = $this->doMatch($pathinfo, $allow, $allowSchemes)) {
            return $ret;
        }
        if ($allow) {
            throw new MethodNotAllowedException(array_keys($allow));
        }
        if (!in_array($this->context->getMethod(), array('HEAD', 'GET'), true)) {
            // no-op
        } elseif ($allowSchemes) {
            redirect_scheme:
            $scheme = $this->context->getScheme();
            $this->context->setScheme(key($allowSchemes));
            try {
                if ($ret = $this->doMatch($pathinfo)) {
                    return $this->redirect($pathinfo, $ret['_route'], $this->context->getScheme()) + $ret;
                }
            } finally {
                $this->context->setScheme($scheme);
            }
        } elseif ('/' !== $pathinfo) {
            $pathinfo = '/' !== $pathinfo[-1] ? $pathinfo.'/' : substr($pathinfo, 0, -1);
            if ($ret = $this->doMatch($pathinfo, $allow, $allowSchemes)) {
                return $this->redirect($pathinfo, $ret['_route']) + $ret;
            }
            if ($allowSchemes) {
                goto redirect_scheme;
            }
        }

        throw new ResourceNotFoundException();
    }

    private function doMatch(string $rawPathinfo, array &$allow = array(), array &$allowSchemes = array()): ?array

EOF
                . $code . "\n        return null;\n    }";
        }

        return "    public function match(\$rawPathinfo)\n" . $code . "\n        throw \$allow ? new MethodNotAllowedException(array_keys(\$allow)) : new ResourceNotFoundException();\n    }";
    }

    /**
     * Generates PHP code to match a RouteCollection with all its routes.
     */
    private function compileRoutes(RouteCollection $routes, bool $matchHost): string
    {
        [$staticRoutes, $dynamicRoutes] = $this->groupStaticRoutes($routes);

        $code = $this->compileStaticRoutes($staticRoutes, $matchHost);
        $chunkLimit = \count($dynamicRoutes);

        while (true) {
            try {
                $this->signalingException = new \RuntimeException('preg_match(): Compilation failed: regular expression is too large');
                $code .= $this->compileDynamicRoutes($dynamicRoutes, $matchHost, $chunkLimit);

                break;
            } catch (\Exception $e) {
                if (1 < $chunkLimit && $this->signalingException === $e) {
                    $chunkLimit = 1 + ($chunkLimit >> 1);

                    continue;
                }

                throw $e;
            }
        }

        // used to display the Welcome Page in apps that don't define a homepage
        $code .= "        if ('/' === \$pathinfo && !\$allow && !\$allowSchemes) {\n";
        $code .= "            throw new Symfony\Component\Routing\Exception\NoConfigurationException();\n";
        $code .= "        }\n";

        return $code;
    }

    /**
     * Splits static routes from dynamic routes, so that they can be matched first, using a simple switch.
     */
    private function groupStaticRoutes(RouteCollection $collection): array
    {
        $staticRoutes = $dynamicRegex = [];
        $dynamicRoutes = new RouteCollection();

        foreach ($collection->all() as $name => $route) {
            $compiledRoute = $route->compile();
            $hostRegex = $compiledRoute->getHostRegex();
            $regex = $compiledRoute->getRegex();
            if (!$compiledRoute->getPathVariables()) {
                $host = !$compiledRoute->getHostVariables() ? $route->getHost() : '';
                $url = $route->getPath();
                foreach ($dynamicRegex as [$hostRx, $rx]) {
                    if (preg_match($rx, $url) && (!$host || !$hostRx || preg_match($hostRx, $host))) {
                        $dynamicRegex[] = [$hostRegex, $regex];
                        $dynamicRoutes->add($name, $route);

                        continue 2;
                    }
                }

                $staticRoutes[$url][$name] = $route;
            } else {
                $dynamicRegex[] = [$hostRegex, $regex];
                $dynamicRoutes->add($name, $route);
            }
        }

        return [$staticRoutes, $dynamicRoutes];
    }

    /**
     * Compiles static routes in a switch statement.
     *
     * Condition-less paths are put in a static array in the switch's default, with generic matching logic.
     * Paths that can match two or more routes, or have user-specified conditions are put in separate switch's cases.
     *
     * @throws \LogicException
     */
    private function compileStaticRoutes(array $staticRoutes, bool $matchHost): string
    {
        if (!$staticRoutes) {
            return '';
        }
        $code = $default = '';

        foreach ($staticRoutes as $url => $routes) {
            if (1 === \count($routes)) {
                foreach ($routes as $name => $route) {
                }

                if (!$route->getCondition()) {
                    $defaults = $route->getDefaults();
                    if (isset($defaults['_canonical_route'])) {
                        $name = $defaults['_canonical_route'];
                        unset($defaults['_canonical_route']);
                    }
                    $default .= sprintf(
                        "%s => array(%s, %s, %s, %s),\n",
                        self::export($url),
                        self::export(['_route' => $name] + $defaults),
                        self::export(!$route->compile()->getHostVariables() ? $route->getHost() : $route->compile()->getHostRegex() ?: null),
                        self::export(array_flip($route->getMethods()) ?: null),
                        self::export(array_flip($route->getSchemes()) ?: null)
                    );

                    continue;
                }
            }

            $code .= sprintf("        case %s:\n", self::export($url));
            foreach ($routes as $name => $route) {
                $code .= $this->compileRoute($route, $name, true);
            }
            $code .= "            break;\n";
        }

        if ($default) {
            $code .= <<<EOF
        default:
            \$routes = array(
{$this->indent($default, 4)}            );

            if (!isset(\$routes[\$pathinfo])) {
                break;
            }
            list(\$ret, \$requiredHost, \$requiredMethods, \$requiredSchemes) = \$routes[\$pathinfo];
{$this->compileSwitchDefault(false, $matchHost)}
EOF;
        }

        return sprintf("        switch (\$pathinfo) {\n%s        }\n\n", $this->indent($code));
    }

    /**
     * Compiles a regular expression followed by a switch statement to match dynamic routes.
     *
     * The regular expression matches both the host and the pathinfo at the same time. For stellar performance,
     * it is built as a tree of patterns, with re-ordering logic to group same-prefix routes together when possible.
     *
     * Patterns are named so that we know which one matched (https://pcre.org/current/doc/html/pcre2syntax.html#SEC23).
     * This name is used to "switch" to the additional logic required to match the final route.
     *
     * Condition-less paths are put in a static array in the switch's default, with generic matching logic.
     * Paths that can match two or more routes, or have user-specified conditions are put in separate switch's cases.
     *
     * Last but not least:
     *  - Because it is not possibe to mix unicode/non-unicode patterns in a single regexp, several of them can be generated.
     *  - The same regexp can be used several times when the logic in the switch rejects the match. When this happens, the
     *    matching-but-failing subpattern is blacklisted by replacing its name by "(*F)", which forces a failure-to-match.
     *    To ease this backlisting operation, the name of subpatterns is also the string offset where the replacement should occur.
     */
    private function compileDynamicRoutes(RouteCollection $collection, bool $matchHost, int $chunkLimit): string
    {
        if (!$collection->all()) {
            return '';
        }
        $code = '';
        $state = (object) [
            'regex' => '',
            'switch' => '',
            'default' => '',
            'mark' => 0,
            'markTail' => 0,
            'hostVars' => [],
            'vars' => [],
        ];
        $state->getVars = static function ($m) use ($state) {
            if ('_route' === $m[1]) {
                return '?:';
            }

            $state->vars[] = $m[1];

            return '';
        };

        $chunkSize = 0;
        $prev = null;
        $perModifiers = [];
        foreach ($collection->all() as $name => $route) {
            preg_match('#[a-zA-Z]*$#', $route->compile()->getRegex(), $rx);
            if ($chunkLimit < ++$chunkSize || $prev !== $rx[0] && $route->compile()->getPathVariables()) {
                $chunkSize = 1;
                $routes = new RouteCollection();
                $perModifiers[] = [$rx[0], $routes];
                $prev = $rx[0];
            }
            $routes->add($name, $route);
        }

        foreach ($perModifiers as [$modifiers, $routes]) {
            $prev = false;
            $perHost = [];
            foreach ($routes->all() as $name => $route) {
                $regex = $route->compile()->getHostRegex();
                if ($prev !== $regex) {
                    $routes = new RouteCollection();
                    $perHost[] = [$regex, $routes];
                    $prev = $regex;
                }
                $routes->add($name, $route);
            }
            $prev = false;
            $rx = '{^(?';
            $code .= "\n            {$state->mark} => " . self::export($rx);
            $state->mark += \strlen($rx);
            $state->regex = $rx;

            foreach ($perHost as [$hostRegex, $routes]) {
                if ($matchHost) {
                    if ($hostRegex) {
                        preg_match('#^.\^(.*)\$.[a-zA-Z]*$#', $hostRegex, $rx);
                        $state->vars = [];
                        $hostRegex = '(?i:' . preg_replace_callback('#\?P<([^>]++)>#', $state->getVars, $rx[1]) . ')\.';
                        $state->hostVars = $state->vars;
                    } else {
                        $hostRegex = '(?:(?:[^./]*+\.)++)';
                        $state->hostVars = [];
                    }
                    $state->mark += \strlen($rx = ($prev ? ')' : '') . "|{$hostRegex}(?");
                    $code .= "\n                ." . self::export($rx);
                    $state->regex .= $rx;
                    $prev = true;
                }

                $tree = new StaticPrefixCollection();
                foreach ($routes->all() as $name => $route) {
                    preg_match('#^.\^(.*)\$.[a-zA-Z]*$#', $route->compile()->getRegex(), $rx);

                    $state->vars = [];
                    $regex = preg_replace_callback('#\?P<([^>]++)>#', $state->getVars, $rx[1]);
                    $tree->addRoute($regex, [$name, $regex, $state->vars, $route]);
                }

                $code .= $this->compileStaticPrefixCollection($tree, $state);
            }
            if ($matchHost) {
                $code .= "\n                .')'";
                $state->regex .= ')';
            }
            $rx = ")$}{$modifiers}";
            $code .= "\n                .'{$rx}',";
            $state->regex .= $rx;
            $state->markTail = 0;

            // if the regex is too large, throw a signaling exception to recompute with smaller chunk size
            set_error_handler(function ($type, $message) { throw 0 === strpos($message, $this->signalingException->getMessage()) ? $this->signalingException : new \ErrorException($message); });

            try {
                preg_match($state->regex, '');
            } finally {
                restore_error_handler();
            }
        }

        if ($state->default) {
            $state->switch .= <<<EOF
        default:
            \$routes = array(
{$this->indent($state->default, 4)}            );

            list(\$ret, \$vars, \$requiredMethods, \$requiredSchemes) = \$routes[\$m];
{$this->compileSwitchDefault(true, $matchHost)}
EOF;
        }

        $matchedPathinfo = $matchHost ? '$host.\'.\'.$pathinfo' : '$pathinfo';
        unset($state->getVars);

        return <<<EOF
        \$matchedPathinfo = {$matchedPathinfo};
        \$regexList = array({$code}
        );

        foreach (\$regexList as \$offset => \$regex) {
            while (preg_match(\$regex, \$matchedPathinfo, \$matches)) {
                switch (\$m = (int) \$matches['MARK']) {
{$this->indent($state->switch, 3)}                }

                if ({$state->mark} === \$m) {
                    break;
                }
                \$regex = substr_replace(\$regex, 'F', \$m - \$offset, 1 + strlen(\$m));
                \$offset += strlen(\$m);
            }
        }

EOF;
    }

    /**
     * Compiles a regexp tree of subpatterns that matches nested same-prefix routes.
     *
     * @param \stdClass $state A simple state object that keeps track of the progress of the compilation,
     *                         and gathers the generated switch's "case" and "default" statements
     */
    private function compileStaticPrefixCollection(StaticPrefixCollection $tree, \stdClass $state, int $prefixLen = 0): string
    {
        $code = '';
        $prevRegex = null;
        $routes = $tree->getRoutes();

        foreach ($routes as $i => $route) {
            if ($route instanceof StaticPrefixCollection) {
                $prevRegex = null;
                $prefix = substr($route->getPrefix(), $prefixLen);
                $state->mark += \strlen($rx = "|{$prefix}(?");
                $code .= "\n                    ." . self::export($rx);
                $state->regex .= $rx;
                $code .= $this->indent($this->compileStaticPrefixCollection($route, $state, $prefixLen + \strlen($prefix)));
                $code .= "\n                    .')'";
                $state->regex .= ')';
                ++$state->markTail;

                continue;
            }

            [$name, $regex, $vars, $route] = $route;
            $compiledRoute = $route->compile();

            if ($compiledRoute->getRegex() === $prevRegex) {
                $state->switch = substr_replace($state->switch, $this->compileRoute($route, $name, false) . "\n", -19, 0);

                continue;
            }

            $state->mark += 3 + $state->markTail + \strlen($regex) - $prefixLen;
            $state->markTail = 2 + \strlen($state->mark);
            $rx = sprintf('|%s(*:%s)', substr($regex, $prefixLen), $state->mark);
            $code .= "\n                    ." . self::export($rx);
            $state->regex .= $rx;
            $vars = array_merge($state->hostVars, $vars);

            if (!$route->getCondition() && (!\is_array($next = $routes[1 + $i] ?? null) || $regex !== $next[1])) {
                $prevRegex = null;
                $defaults = $route->getDefaults();
                if (isset($defaults['_canonical_route'])) {
                    $name = $defaults['_canonical_route'];
                    unset($defaults['_canonical_route']);
                }
                $state->default .= sprintf(
                    "%s => array(%s, %s, %s, %s),\n",
                    $state->mark,
                    self::export(['_route' => $name] + $defaults),
                    self::export($vars),
                    self::export(array_flip($route->getMethods()) ?: null),
                    self::export(array_flip($route->getSchemes()) ?: null)
                );
            } else {
                $prevRegex = $compiledRoute->getRegex();
                $combine = '            $matches = array(';
                foreach ($vars as $j => $m) {
                    $combine .= sprintf('%s => $matches[%d] ?? null, ', self::export($m), 1 + $j);
                }
                $combine = $vars ? substr_replace($combine, ");\n\n", -2) : '';

                $state->switch .= <<<EOF
        case {$state->mark}:
{$combine}{$this->compileRoute($route, $name, false)}
            break;

EOF;
            }
        }

        return $code;
    }

    /**
     * A simple helper to compiles the switch's "default" for both static and dynamic routes.
     */
    private function compileSwitchDefault(bool $hasVars, bool $matchHost): string
    {
        if ($hasVars) {
            $code = <<<EOF

            foreach (\$vars as \$i => \$v) {
                if (isset(\$matches[1 + \$i])) {
                    \$ret[\$v] = \$matches[1 + \$i];
                }
            }

EOF;
        } elseif ($matchHost) {
            $code = <<<EOF

            if (\$requiredHost) {
                if ('#' !== \$requiredHost[0] ? \$requiredHost !== \$host : !preg_match(\$requiredHost, \$host, \$hostMatches)) {
                    break;
                }
                if ('#' === \$requiredHost[0] && \$hostMatches) {
                    \$hostMatches['_route'] = \$ret['_route'];
                    \$ret = \$this->mergeDefaults(\$hostMatches, \$ret);
                }
            }

EOF;
        } else {
            $code = '';
        }

        $code .= <<<EOF

            \$hasRequiredScheme = !\$requiredSchemes || isset(\$requiredSchemes[\$context->getScheme()]);
            if (\$requiredMethods && !isset(\$requiredMethods[\$canonicalMethod]) && !isset(\$requiredMethods[\$requestMethod])) {
                if (\$hasRequiredScheme) {
                    \$allow += \$requiredMethods;
                }
                break;
            }
            if (!\$hasRequiredScheme) {
                \$allowSchemes += \$requiredSchemes;
                break;
            }

            return \$ret;

EOF;

        return $code;
    }

    /**
     * Compiles a single Route to PHP code used to match it against the path info.
     *
     * @throws \LogicException
     */
    private function compileRoute(Route $route, string $name, bool $checkHost): string
    {
        $code = '';
        $compiledRoute = $route->compile();
        $conditions = [];
        $matches = (bool) $compiledRoute->getPathVariables();
        $hostMatches = (bool) $compiledRoute->getHostVariables();
        $methods = array_flip($route->getMethods());

        if ($route->getCondition()) {
            $expression = $this->getExpressionLanguage()->compile($route->getCondition(), ['context', 'request']);

            if (false !== strpos($expression, '$request')) {
                $conditions[] = '($request = $request ?? $this->request ?: $this->createRequest($pathinfo))';
            }
            $conditions[] = $expression;
        }

        if (!$checkHost || !$compiledRoute->getHostRegex()) {
            // no-op
        } elseif ($hostMatches) {
            $conditions[] = sprintf('preg_match(%s, $host, $hostMatches)', self::export($compiledRoute->getHostRegex()));
        } else {
            $conditions[] = sprintf('%s === $host', self::export($route->getHost()));
        }

        $conditions = implode(' && ', $conditions);

        if ($conditions) {
            $code .= <<<EOF
        // $name
        if ($conditions) {

EOF;
        } else {
            $code .= "            // {$name}\n";
        }

        $gotoname = 'not_' . preg_replace('/[^A-Za-z0-9_]/', '', $name);

        // the offset where the return value is appended below, with indendation
        $retOffset = 12 + \strlen($code);
        $defaults = $route->getDefaults();
        if (isset($defaults['_canonical_route'])) {
            $name = $defaults['_canonical_route'];
            unset($defaults['_canonical_route']);
        }

        // optimize parameters array
        if ($matches || $hostMatches) {
            $vars = ["array('_route' => '$name')"];
            if ($matches || ($hostMatches && !$checkHost)) {
                $vars[] = '$matches';
            }
            if ($hostMatches && $checkHost) {
                $vars[] = '$hostMatches';
            }

            $code .= sprintf(
                "            \$ret = \$this->mergeDefaults(%s, %s);\n",
                implode(' + ', $vars),
                self::export($defaults)
            );
        } elseif ($defaults) {
            $code .= sprintf("            \$ret = %s;\n", self::export(['_route' => $name] + $defaults));
        } else {
            $code .= sprintf("            \$ret = array('_route' => '%s');\n", $name);
        }

        if ($methods) {
            $methodVariable = isset($methods['GET']) ? '$canonicalMethod' : '$requestMethod';
            $methods = self::export($methods);
        }

        if ($schemes = $route->getSchemes()) {
            $schemes = self::export(array_flip($schemes));
            if ($methods) {
                $code .= <<<EOF
            \$requiredSchemes = $schemes;
            \$hasRequiredScheme = isset(\$requiredSchemes[\$context->getScheme()]);
            if (!isset((\$a = {$methods})[{$methodVariable}])) {
                if (\$hasRequiredScheme) {
                    \$allow += \$a;
                }
                goto $gotoname;
            }
            if (!\$hasRequiredScheme) {
                \$allowSchemes += \$requiredSchemes;
                goto $gotoname;
            }


EOF;
            } else {
                $code .= <<<EOF
            \$requiredSchemes = $schemes;
            if (!isset(\$requiredSchemes[\$context->getScheme()])) {
                \$allowSchemes += \$requiredSchemes;
                goto $gotoname;
            }


EOF;
            }
        } elseif ($methods) {
            $code .= <<<EOF
            if (!isset((\$a = {$methods})[{$methodVariable}])) {
                \$allow += \$a;
                goto $gotoname;
            }


EOF;
        }

        if ($schemes || $methods) {
            $code .= "            return \$ret;\n";
        } else {
            $code = substr_replace($code, 'return', $retOffset, 6);
        }
        if ($conditions) {
            $code .= "        }\n";
        } elseif ($schemes || $methods) {
            $code .= '    ';
        }

        if ($schemes || $methods) {
            $code .= "        $gotoname:\n";
        }

        return $conditions ? $this->indent($code) : $code;
    }

    private function getExpressionLanguage()
    {
        if (null === $this->expressionLanguage) {
            if (!class_exists('Symfony\Component\ExpressionLanguage\ExpressionLanguage')) {
                throw new \RuntimeException('Unable to use expressions as the Symfony ExpressionLanguage component is not installed.');
            }
            $this->expressionLanguage = new ExpressionLanguage(null, $this->expressionLanguageProviders);
        }

        return $this->expressionLanguage;
    }

    private function indent($code, $level = 1)
    {
        return preg_replace('/^./m', str_repeat('    ', $level) . '$0', $code);
    }

    /**
     * @internal
     */
    public static function export($value): string
    {
        if (null === $value) {
            return 'null';
        }
        if (!\is_array($value)) {
            if (\is_object($value)) {
                throw new \InvalidArgumentException('Symfony\Component\Routing\Route cannot contain objects.');
            }

            return str_replace("\n", '\'."\n".\'', var_export($value, true));
        }
        if (!$value) {
            return 'array()';
        }

        $i = 0;
        $export = 'array(';

        foreach ($value as $k => $v) {
            if ($i === $k) {
                ++$i;
            } else {
                $export .= self::export($k) . ' => ';

                if (\is_int($k) && $i < $k) {
                    $i = 1 + $k;
                }
            }

            $export .= self::export($v) . ', ';
        }

        return substr_replace($export, ')', -2);
    }
}
