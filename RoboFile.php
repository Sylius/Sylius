<?php

use Robo\Exception\TaskException;
use Robo\Result;
use Robo\Symfony\ConsoleIO;
use Robo\Tasks;

class RoboFile extends Tasks
{
    const ROOT_DIR = __DIR__;

    const SUCCESS = 'success';

    const FAILED = 'failed';

    const YES = 'yes';

    public function ciPackages(ConsoleIO $io, string $packagesJson): ?Result
    {
        $packages = json_decode($packagesJson, true);
        $result = [];
        $failed = false;

        foreach ($packages as $package) {
            $this->startGroup($package);

            try {
                $processResult = $this->processPackagePipeline($package);
                $result[$package] = null !== $processResult && $processResult->wasSuccessful() ? self::SUCCESS : self::FAILED;
            } catch (TaskException) {
                $result[$package] = self::FAILED;
            }
        }

        $this->endGroup();

        foreach ($result as $packageName => $value) {
            printf("%s %s%s", $value === self::SUCCESS ? '✅' : '❌', $packageName, PHP_EOL);
            $failed = $failed || $value === self::FAILED;
        }

        exit(false === $failed ? 0 : 1);
    }

    /**
     * @throws TaskException
     */
    private function processPackagePipeline(string $package): ?Result
    {
        $symfonyVersion = getenv('SYMFONY_VERSION');
        $doctrineORMVersion = getenv('DOCTRINE_ORM_VERSION');
        $unstable = getenv('UNSTABLE');
        $packagePath = sprintf('%s/src/Sylius/%s', self::ROOT_DIR, $package);
        $composerJsonPath = sprintf('%s/composer.json', $packagePath);
        $requiresDoctrineORM = false;

        if (false === $symfonyVersion) {
            throw new RuntimeException('SYMFONY_VERSION environment variable is not set.');
        }

        if (false === $doctrineORMVersion) {
            throw new RuntimeException('DOCTRINE_ORM_VERSION environment variable is not set.');
        }

        if (!file_exists($composerJsonPath)) {
            throw new RuntimeException('composer.json file does not exist.');
        }

        $task = $this->taskExecStack()
            ->dir($packagePath)
            ->stopOnFail()
            ->exec(sprintf('composer config extra.symfony.require "%s"', $symfonyVersion))
        ;

        if (self::YES === $unstable) {
            $task->exec('composer config minimum-stability dev');
            $task->exec('composer config prefer-stable true');
        }

        $composerData = json_decode(file_get_contents($composerJsonPath), true);
        $require = $composerData['require'] ?? [];
        $requireDev = $composerData['require-dev'] ?? [];

        $existsOnRequire = array_key_exists('doctrine/orm', $require);
        $existsOnRequireDev = array_key_exists('doctrine/orm', $requireDev);

        $requiresDoctrineORM = $existsOnRequire || $existsOnRequireDev;

        if ('' !== $doctrineORMVersion && $requiresDoctrineORM) {
            $task
                ->exec(sprintf(
                    'composer require %s doctrine/orm "%s" --no-update --no-scripts --no-interaction',
                    $existsOnRequireDev ? '--dev' : '',
                    $doctrineORMVersion,
                ));
        }

        $task
            ->exec('composer update --no-scripts --no-interaction')
            ->exec('composer validate --ansi --strict')
        ;

        if (in_array($package, ['Bundle/AdminBundle', 'Bundle/ApiBundle', 'Bundle/CoreBundle'])) {
            $this->createTestAssets(sprintf('%s/tests/Application', $packagePath));
            $this->createTestAssets(sprintf('%s/test', $packagePath)); // Remove after all test apps have been moved
        }

        if ('Bundle/ApiBundle' === $package) {
            $task->exec('tests/Application/bin/console doctrine:schema:update --force');
        }

        if (file_exists(sprintf('%s/phpunit.xml', $packagePath)) || file_exists(sprintf('%s/phpunit.xml.dist', $packagePath))) {
            $task->exec('vendor/bin/phpunit --colors=always');
        }

        return $task->run();
    }

    private function createTestAssets(string $testAppDirectory): void
    {
        $adminBuildDir = sprintf('%s/public/build/admin', $testAppDirectory);
        $shopBuildDir = sprintf('%s/public/build/shop', $testAppDirectory);

        if (!file_exists($adminBuildDir)) {
            mkdir($adminBuildDir, 0777, true);
            file_put_contents(sprintf('%s/manifest.json', $adminBuildDir), '{}');
        }

        if (!file_exists($shopBuildDir)) {
            mkdir($shopBuildDir, 0777, true);
            file_put_contents(sprintf('%s/manifest.json', $shopBuildDir), '{}');
        }
    }

    private function startGroup(string $groupName): void
    {
        printf("::group::%s\n", $groupName);
    }

    private function endGroup(): void
    {
        echo "\n::endgroup::\n\n";
    }
}
