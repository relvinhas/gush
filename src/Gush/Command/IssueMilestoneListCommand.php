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

/**
 * Lists the milestones for the issues
 *
 * @author Daniel Gomes <me@danielcsgomes.com>
 */
class IssueMilestoneListCommand extends BaseRepoCommand
{
    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        parent::configure();

        $this
            ->setName('issue:list:milestones')
            ->setDescription('List of the issue\'s milestones')
        ;
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        list($organization, $repository) = $this->getOrgAndRepo($input);

        $client = $this->getGithubClient();
        $milestones = $client->api('issue')->milestones()->all($organization, $repository);

        $table = $this->getHelper('table');
        $table->setLayout('compact');
        $table->formatRows($milestones, $this->getRowBuilderCallback());
        $table->render($output, $table);

        return $milestones;
    }

    private function getRowBuilderCallback()
    {
        return function ($milestone) {
            return [$milestone['title']];
        };
    }
}
