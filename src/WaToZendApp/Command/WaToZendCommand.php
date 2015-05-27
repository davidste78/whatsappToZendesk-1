<?php

namespace WaToZendApp\Command;

use Lib\Config;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class WaToZendCommand extends Command
{

    protected function configure()
    {
        $this
            ->setName('whatsapp:client:start')
            ->setDescription('Start the whatsapp client')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $username = (string) Config::get('whatsapp.ng.phone');
        $password = (string) Config::get('whatsapp.ng.password');
        $nickname = (string) Config::get('whatsapp.ng.nickname');
        $debug    = false;

        $w = new \WhatsProt($username, $nickname, $debug);

        $w->connect();
        $w->loginWithPassword($password);

        $output->writeln('[] Listen mode:');
        while (TRUE) {
            $w->PollMessage();
            $data = $w->GetMessages();
            if (!empty($data)) {
                foreach ($data as $object) {
                    $message    = $object->getChild('body');
                    $message    = $message->getData();
                    $fromName   = $object->getAttribute("notify");
                    $fromNumber = $object->getAttribute("from");
//                    $output->writeln($object->nodeString());
                    $output->writeln("$fromNumber - $fromName : $message");
                }
            }
            sleep(1);
        }
    }
}
