<?php

namespace ZDiet\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Created by IntelliJ IDEA.
 * User: yui_tang
 * Date: 1/20/16
 * Time: 14:30
 */
class PostToSlackCommand extends Command
{
    protected function configure()
    {
        $this
            ->setName('z-diet:post_to_slack')
            ->setDescription('Post to slack.')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $output->writeln("done.");
    }
}