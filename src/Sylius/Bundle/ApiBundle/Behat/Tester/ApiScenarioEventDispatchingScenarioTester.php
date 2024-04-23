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

namespace Sylius\Bundle\ApiBundle\Behat\Tester;

use Behat\Behat\Tester\ScenarioTester;
use Behat\Gherkin\Node\FeatureNode;
use Behat\Gherkin\Node\ScenarioInterface as Scenario;
use Behat\Gherkin\Node\ScenarioNode;
use Behat\Testwork\Environment\Environment;
use Behat\Testwork\Suite\Exception\ParameterNotFoundException;
use Behat\Testwork\Tester\Result\TestResult;
use Behat\Testwork\Tester\Setup\Setup;
use Behat\Testwork\Tester\Setup\Teardown;

final class ApiScenarioEventDispatchingScenarioTester implements ScenarioTester
{
    public function __construct(private ScenarioTester $baseTester)
    {
    }

    public function setUp(Environment $env, FeatureNode $feature, Scenario $scenario, $skip): Setup
    {
        try {
            if ($env->getSuite()->getSetting('javascript')) {
                return $this->baseTester->setUp($env, $feature, $scenario, $skip);
            }
        } catch (ParameterNotFoundException) {
            return $this->baseTester->setUp($env, $feature, $scenario, $skip);
        }

        $tags = $scenario->getTags();
        if (($key = array_search('javascript', $tags)) !== false) {
            unset($tags[$key]);
        }

        $scenario = new ScenarioNode(
            $scenario->getTitle(),
            $tags,
            $scenario->getSteps(),
            $scenario->getKeyword(),
            $scenario->getLine(),
        );

        return $this->baseTester->setUp($env, $feature, $scenario, $skip);
    }

    public function test(Environment $env, FeatureNode $feature, Scenario $scenario, $skip): TestResult
    {
        return $this->baseTester->test($env, $feature, $scenario, $skip);
    }

    public function tearDown(Environment $env, FeatureNode $feature, Scenario $scenario, $skip, TestResult $result): Teardown
    {
        return $this->baseTester->tearDown($env, $feature, $scenario, $skip, $result);
    }
}
