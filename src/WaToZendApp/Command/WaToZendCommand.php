<?php

namespace WaToZendApp\Command;

use Lib\Config;
use Lib\ZendeskClient;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class WaToZendCommand extends Command
{
    /** @var string */
    private $country;
    /** @var bool */
    private $debug = false;
    /** @var InputInterface */
    private $input;
    /** @var OutputInterface */
    private $output;

    protected function configure()
    {
        $this
            ->setName('whatsapp:client:start')
            ->setDescription('Start the whatsapp client')
            ->addArgument(
                'country',
                InputArgument::REQUIRED,
                'country code 2 letters'
            )
            ->addOption(
                'debug',
                null,
                InputOption::VALUE_NONE,
                'enable debug mode'
            )
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->input    = $input;
        $this->output   = $output;
        $this->country  = $input->getArgument('country');
        $username       = (string) Config::get("$this->country.whatsapp.phone");
        $password       = (string) Config::get("$this->country.whatsapp.password");
        $nickname       = (string) Config::get("$this->country.whatsapp.nickname");

        if ($input->getOption('debug')) {
            $this->debug = true;
        }

        $w = new \WhatsProt($username, $nickname, $this->debug);
        $w->connect();
        $w->loginWithPassword($password);

        $output->writeln('[] Listen mode:');
        while (TRUE) {
            $w->PollMessage();
            $data = $w->GetMessages();
            if (!empty($data)) {
                foreach ($data as $object) {
                    $this->sendComment($object);
                }
            }
            sleep(1);
        }
    }

    protected function sendComment(\ProtocolNode $node)
    {
        $client     = new ZendeskClient($this->country);
        $newTicket  = $client->manage($node);
        $this->output->writeln($newTicket->ticket->id . " ticket generated/edited");
    }
}
