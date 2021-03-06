<?php

namespace WaToZendApp\Command;

use Lib\Config;
use Lib\ZendeskClient;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class SetStatusCommand extends Command
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
            ->setName('whatsapp:set:status')
            ->setDescription('Set whatsapp status')
            ->addArgument(
                'country',
                InputArgument::REQUIRED,
                'country code 2 letters'
            )
            ->addArgument(
                'text',
                InputArgument::REQUIRED,
                'status text to display'
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
        $this->text     = $input->getArgument('text');
        $username       = (string) Config::get("$this->country.whatsapp.phone");
        $password       = (string) Config::get("$this->country.whatsapp.password");
        $nickname       = (string) Config::get("$this->country.whatsapp.nickname");

        if ($input->getOption('debug')) {
            $this->debug = true;
        }

        $w = new \WhatsProt($username, $nickname, $this->debug);
        $w->connect();
        $w->loginWithPassword($password);

        try {
            $w->sendStatusUpdate($this->text);
            $this->output->writeln('Status updated');
        } catch (Exception $e) {
            $this->output->writeln('Status was not updated. Try again');
        }

    }

}
