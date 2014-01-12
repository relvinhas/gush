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
 * Lists the labels for the issues
 *
 * @author Daniel Gomes <me@danielcsgomes.com>
 */
class IssueLabelListCommand extends BaseRepoCommand
{
    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        parent::configure();

        $this
            ->setName('issue:label:list')
            ->setDescription('List of the issue\'s labels')
        ;
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        list($organization, $repository) = $this->getOrgAndRepo($input);

        $client = $this->getGithubClient();

        $labels = $client->api('issue')->labels()->all($organization, $repository);

        $table = $this->getHelper('table');
        $table->setLayout('compact');
        $table->formatRows($labels, function ($label) {
            return [$label['name']];
        });
        $table->render($output, $table);

        return $labels;
    }
}
