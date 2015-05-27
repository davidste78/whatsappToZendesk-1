<?php

namespace Lib;

use Zendesk\API\Client as ZendeskAPI;

Class ZendeskClient
{
    private $client = array();

    public function __construct($country)
    {
        $subDomain = Config::get("zendesk.$country.sub_domain");
        $username  = Config::get("zendesk.$country.username");
        $token     = Config::get("zendesk.$country.token");

        $this->client = new ZendeskAPI($subDomain, $username);
        $this->client->setAuth('token', $token);
    }

    public function getClient()
    {
        return $this->client;
    }

    public function create()
    {
        return $this->client->tickets()->create(
            array(
                'subject' => '[whatsapp] The quick brown fox jumps over the lazy dog',
                'comment' => array (
                    'body' => 'Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua.'
                ),
                'priority' => 'normal'
            )
        );
    }
}
