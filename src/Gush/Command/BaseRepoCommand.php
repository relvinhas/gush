<?php

namespace Gush\Command;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Process\Process;

class BaseRepoCommand extends BaseCommand
{
    protected function configure()
    {
        $this->addOption('repo', 'r', InputOption::VALUE_REQUIRED, 
            'Repository, <orgname>/<repo>'
        );
    }

    public function getOrgAndRepo(InputInterface $input)
    {
        $orgRepoName = $input->getOption('repo');

        if ($orgRepoName) {
            $list = explode('/', $orgRepoName);
            if (count($list) != 2) {
                throw new \InvalidArgumentException(sprintf(
                    '"%s" is an invalid repository name, it should be formatted as "<org_name>/<repository_name>".',
                    $orgRepoName
                ));
            }
        } else {
            $list = array(
                $this->getGitOrgName(),
                $this->getGitRepoName(),
            );
        }

        return $list;
    }

    /**
     * @return string The repository name
     */
    protected function getGitRepoName()
    {
        $process = new Process('git remote show -n origin | grep Fetch | cut -d "/" -f 2 | cut -d "." -f 1', getcwd());
        $process->run();

        $output = trim($process->getOutput());
        if (empty($output)) {
            $process = new Process('git remote show -n origin | grep Fetch | cut -d "/" -f 5 | cut -d "." -f 1', getcwd());
            $process->run();
        }

        return trim($process->getOutput());
    }

    /**
     * @return string The vendor name
     */
    protected function getGitOrgName()
    {
        $process = new Process('git remote show -n origin | grep Fetch | cut -d ":" -f 3 | cut -d "/" -f 1', getcwd());
        $process->run();

        $output = trim($process->getOutput());
        if (empty($output)) {
            $process = new Process('git remote show -n origin | grep Fetch | cut -d ":" -f 3 | cut -d "/" -f 4', getcwd());
            $process->run();
        }

        return trim($process->getOutput());
    }
}
