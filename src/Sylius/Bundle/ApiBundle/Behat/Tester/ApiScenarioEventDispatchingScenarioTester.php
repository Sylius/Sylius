<?php

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
    /** @var ScenarioTester */
    private $baseTester;

    public function __construct(ScenarioTester $baseTester)
    {
        $this->baseTester = $baseTester;
    }

    public function setUp(Environment $env, FeatureNode $feature, Scenario $scenario, $skip): Setup
    {
        try {
            $javascript = $env->getSuite()->getSetting('javascript');
        } catch (ParameterNotFoundException $exception) {
            return $this->baseTester->setUp($env, $feature, $scenario, $skip);
        }

        if (!$javascript) {
            $tags = $scenario->getTags();
            if (($key = array_search('javascript', $tags)) !== false) {
                unset($tags[$key]);
            }

            $scenario = new ScenarioNode(
                $scenario->getTitle(),
                $tags,
                $scenario->getSteps(),
                $scenario->getKeyword(),
                $scenario->getLine()
            );
        }

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
