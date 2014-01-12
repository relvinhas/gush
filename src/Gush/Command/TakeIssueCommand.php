<?php

/*
 * This file is part of Gush.
 *
 * (c) Luis Cordova <cordoval@gmail.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Gush\Command;

use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class TakeIssueCommand extends BaseRepoCommand
{
    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        parent::configure();

        $this
            ->setName('pull-request:take')
            ->setDescription('Take an issue')
            ->addArgument('issue_number', InputArgument::REQUIRED, 'Number of the issue')
            ->addArgument('base_branch', InputArgument::OPTIONAL, 'Name of the base branch to checkout from', 'master')
        ;
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $issueNumber = $input->getArgument('issue_number');
        $baseBranch = $input->getArgument('base_branch');
        list($org, $repo) = $this->getOrgAndRepo($input);

        $client = $this->getGithubClient();
        $issue = $client
            ->api('issue')
            ->show($org, $repo, $issueNumber)
        ;

        $slugTitle = $this
            ->getSlugifier()
            ->slugify(
                explode(' ', $issueNumber.' '.$issue['title'])
            )
        ;

        $commands = [
            [
                'line' => 'git remote update',
                'allow_failures' => true
            ],
            [
                'line' => sprintf('git checkout %s/%s', 'origin', $baseBranch),
                'allow_failures' => true
            ],
            [
                'line' => sprintf('git checkout -b %s', $slugTitle),
                'allow_failures' => true
            ],
        ];

        $this->runCommands($commands);
    }
}
