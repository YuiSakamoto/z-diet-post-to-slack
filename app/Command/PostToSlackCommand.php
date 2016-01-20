<?php

namespace ZDiet\Command;

use Dietcube\Dispatcher;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use ZDiet\Application;
use ZDiet\Service\BodyMeasureService;
use ZDiet\Service\UserService;
use ZDiet\Service\PostService;

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
        $app = new Application(dirname(dirname(__DIR__) . '/app'), Dispatcher::getEnv());
        $dispatcher = new Dispatcher($app);
        $dispatcher->boot();

        $user_service = new UserService();
        $body_measure_service = new BodyMeasureService($app->getContainer(), $app->getTmpDir());
        $users = $user_service->getUsers();
        $response = $body_measure_service->getData($users);
        $post_service = new PostService($app->getContainer());
        $post_service->post($response);
    }
}