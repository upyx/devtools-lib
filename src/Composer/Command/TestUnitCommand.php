<?php

/**
 * This file is part of ramsey/devtools-lib
 *
 * ramsey/devtools-lib is open source software: you can distribute
 * it and/or modify it under the terms of the MIT License
 * (the "License"). You may not use this file except in
 * compliance with the License.
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or
 * implied. See the License for the specific language governing
 * permissions and limitations under the License.
 *
 * @copyright Copyright (c) Ben Ramsey <ben@benramsey.com>
 * @license https://opensource.org/licenses/MIT MIT License
 */

declare(strict_types=1);

namespace Ramsey\Dev\Tools\Composer\Command;

use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

use function array_merge;

class TestUnitCommand extends ProcessCommand
{
    public function getBaseName(): string
    {
        return 'test:unit';
    }

    /**
     * @inheritDoc
     */
    public function getProcessCommand(InputInterface $input, OutputInterface $output): array
    {
        /** @var string[] $args */
        $args = $input->getArguments()['args'] ?? [];

        if ($input->getOption('phpunit-help')) {
            // Ignore all other arguments and display phpunit help.
            $args = ['--help'];
        }

        return array_merge(
            [
                $this->withBinPath('phpunit'),
                '--colors=always',
            ],
            $args,
        );
    }

    protected function configure(): void
    {
        $this
            ->setDescription('Runs unit tests.')
            ->addUsage('--phpunit-help')
            ->addUsage('-- [<phpunit-options>...]')
            ->addUsage('-- [<file>...]')
            ->setHelp($this->getHelpText())
            ->setDefinition([
                new InputArgument('args', InputArgument::IS_ARRAY | InputArgument::OPTIONAL, ''),
                new InputOption('phpunit-help', null, InputOption::VALUE_NONE, 'Display phpunit help'),
            ]);
    }

    private function getHelpText(): string
    {
        return <<<'EOD'
            The <info>%command.name%</info> command executes <info>phpunit</info>. It uses any local
            configuration files (e.g., phpunit.xml) available.

            For more information on phpunit, see https://phpunit.de

            You may also pass additional arguments to phpunit. To do so, use a
            double-dash (<info>--</info>) to indicate all following arguments and options
            should be passed along directly to phpunit.

            For example:

              <info>%command.full_name% -- tests/File1Test.php tests/File2Test.php</info>

            To view phpunit help, use the <info>--phpunit-help</info> option.

            <comment>Please Note:</comment> Composer captures some options early and, therefore,
            cannot easily pass them along to phpunit. These include standard
            options such as <info>--help</info>, <info>--version</info>, and <info>--quiet</info>. To use these options,
            invoke phpunit directly via <info>./vendor/bin/phpunit</info>.
            EOD;
    }
}
