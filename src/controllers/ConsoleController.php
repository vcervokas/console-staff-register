<?php

namespace controllers;

use entities\PersonEntity;
use managers\ConsoleManager;
use managers\CsvDatabaseManager;

/**
 * Class ConsoleController.
 */
class ConsoleController
{
    /**
     * @param $argc
     * @param $argv
     */
    public function handleRequests($argc, $argv): void
    {
        $db = new CsvDatabaseManager('staff.csv', PersonEntity::class);
        $console = new ConsoleManager($db);

        $command = $this->extractConsoleCommand($argv);
        $arguments = $this->extractConsoleArguments($argv);

        try {
            if (method_exists(get_class($console), $command)) {
                $console->$command($arguments);
            } else {
                $console->help();
            }
        } catch (\Exception $e) {
            echo $e->getMessage();
        }
    }

    /**
     * @param array $argv
     *
     * @return array|null
     */
    private function extractConsoleArguments(array $argv): ?array
    {
        unset($argv[0]);
        if (isset($argv[1])) {
            unset($argv[1]);
        }

        return array_values($argv);
    }

    /**
     * @param array $argv
     *
     * @return null|string
     */
    private function extractConsoleCommand(array $argv): ?string
    {
        return isset($argv[1]) ? $argv[1] : null;
    }
}
