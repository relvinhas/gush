<?php

/*
 * This file is part of Gush.
 *
 * (c) Luis Cordova <cordoval@gmail.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Gush;

use Github\Client;
use Github\HttpClient\CachedHttpClient;

use Gush\Command\ConfigureCommand;
use Gush\Command\CsFixerCommand;
use Gush\Command\FabbotIoCommand;
use Gush\Command\IssueCloseCommand;
use Gush\Command\IssueCreateCommand;
use Gush\Command\IssueLabelListCommand;
use Gush\Command\IssueListCommand;
use Gush\Command\IssueMilestoneListCommand;
use Gush\Command\IssueShowCommand;
use Gush\Command\LabelIssuesCommand;
use Gush\Command\PullRequestCreateCommand;
use Gush\Command\PullRequestMergeCommand;
use Gush\Command\ReleaseCreateCommand;
use Gush\Command\ReleaseListCommand;
use Gush\Command\ReleaseRemoveCommand;
use Gush\Command\SquashCommand;
use Gush\Command\SwitchBaseCommand;
use Gush\Command\SyncCommand;
use Gush\Command\TakeIssueCommand;

use Gush\Event\CommandEvent;
use Gush\Event\GushEvents;
use Gush\Exception\FileNotFoundException;
use Gush\Helper\GitHelper;
use Gush\Helper\TableHelper;
use Gush\Helper\TextHelper;
use Gush\Subscriber\GitHubSubscriber;
use Gush\Subscriber\TableSubscriber;
use Symfony\Component\Console\Application as BaseApplication;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\EventDispatcher\EventDispatcher;
use Symfony\Component\Yaml\Yaml;

class Application extends BaseApplication
{
    /**
     * @var Config $config The configuration file
     */
    protected $config;

    /**
     * @var \Github\Client $githubClient The Github Client
     */
    protected $githubClient = null;

    /**
     * @var \Symfony\Component\EventDispatcher\EventDispatcher
     */
    protected $dispatcher;

    public function __construct()
    {
        // instantiate the helpers here so that
        // we can use them in the subscribers.
        $this->gitHelper = new GitHelper();
        $this->textHelper = new TextHelper();
        $this->tableHelper = new TableHelper();

        // the parent dispatcher is private and has
        // no accessor, so we set it here so we can access it.
        $this->dispatcher = new EventDispatcher();

        // add our subscribers to the event dispatcher
        $this->dispatcher->addSubscriber(new TableSubscriber());
        $this->dispatcher->addSubscriber(new GitHubSubscriber($this->gitHelper));

        // share our dispatcher with the parent class
        $this->setDispatcher($this->dispatcher);

        parent::__construct();

        $this->add(new TakeIssueCommand());
        $this->add(new PullRequestCreateCommand());
        $this->add(new PullRequestMergeCommand());
        $this->add(new SwitchBaseCommand());
        $this->add(new SquashCommand());
        $this->add(new FabbotIoCommand());
        $this->add(new CsFixerCommand());
        $this->add(new ReleaseCreateCommand());
        $this->add(new ReleaseListCommand());
        $this->add(new ReleaseRemoveCommand());
        $this->add(new IssueCreateCommand());
        $this->add(new IssueCloseCommand());
        $this->add(new IssueLabelListCommand());
        $this->add(new IssueMilestoneListCommand());
        $this->add(new IssueShowCommand());
        $this->add(new IssueListCommand());
        $this->add(new SyncCommand());
        $this->add(new LabelIssuesCommand());
        $this->add(new ConfigureCommand());
    }

    public function add(Command $command)
    {
        $this->dispatcher->dispatch(
            GushEvents::DECORATE_DEFINITION,
            new CommandEvent($command)
        );

        parent::add($command);
    }

    protected function getDefaultHelperSet()
    {
        $helperSet = parent::getDefaultHelperSet();
        $helperSet->set($this->gitHelper);
        $helperSet->set($this->textHelper);
        $helperSet->set($this->tableHelper);

        return $helperSet;
    }

    public function setGithubClient(Client $githubClient)
    {
        $this->githubClient = $githubClient;
    }

    /**
     * {@inheritdoc}
     */
    protected function doRunCommand(Command $command, InputInterface $input, OutputInterface $output)
    {
        if ('configure' !== $this->getCommandName($input)) {
            $this->readParameters();

            if (null === $this->githubClient) {
                $this->githubClient = $this->buildGithubClient();
            }
        }

        parent::doRunCommand($command, $input, $output);
    }

    /**
     * @return \Github\Client
     */
    public function getGithubClient()
    {
        return $this->githubClient;
    }

    protected function readParameters()
    {
        $this->config = Factory::createConfig();

        $localFilename = $this->config->get('home').'/.gush.yml';

        if (!file_exists($localFilename)) {
            throw new FileNotFoundException(
                'The \'.gush.yml\' doest not exist, please run the \'configure\' command.'
            );
        }

        try {
            $yaml = new Yaml();
            $parsed = $yaml->parse($localFilename);
            $this->config->merge($parsed['parameters']);

            if (!$this->config->isValid()) {
                throw new \RuntimeException(
                    "The '.gush.yml' is not properly configured. Please run the 'configure' command."
                );
            }
        } catch (\Exception $e) {
            throw new \RuntimeException("{$e->getMessage()}.\nPlease run 'configure' command.");
        }
    }

    protected function buildGithubClient()
    {
        $cachedClient = new CachedHttpClient([
            'cache_dir' => $this->config->get('cache-dir')
        ]);

        $githubCredentials = $this->config->get('github');

        $githubClient = new Client($cachedClient);
        $githubClient->authenticate(
            $githubCredentials['username'],
            $githubCredentials['password'],
            Client::AUTH_HTTP_PASSWORD
        );

        return $githubClient;
    }

    public function getConfig()
    {
        return $this->config;
    }

    public function getDispatcher()
    {
        return $this->dispatcher;
    }
}
