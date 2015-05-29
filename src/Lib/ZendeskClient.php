<?php

namespace Lib;

use Zendesk\API\Client as ZendeskAPI;

Class ZendeskClient
{
    /** @var \Zendesk\API\Client */
    private $client;

    private $brandId;

    public function __construct($country)
    {
        $subDomain      = Config::get("$country.zendesk.sub_domain");
        $username       = Config::get("$country.zendesk.username");
        $token          = Config::get("$country.zendesk.token");
        $this->brandId  = Config::get("$country.zendesk.brand_id");

        $this->client = new ZendeskAPI($subDomain, $username);
        $this->client->setAuth('token', $token);
    }

    public function getClient()
    {
        return $this->client;
    }

    public function manage(\ProtocolNode $node)
    {
        $message    = $node->getChild('body');
        $message    = $message->getData();
        $fromName   = $node->getAttribute("notify");
        $fromNumber = $node->getAttribute("from");
        $ticket     = $this->hasOneOpen($fromNumber);

        if ($ticket) {
            $result = $this->client->ticket()->update(
                array(
                    'id' => $ticket->id,
                    "comment" => array(
                        "public" => true,
                        "body"   => $message
                    ),
                    "requester" => array(
                        "name"  => $fromName,
                        "email" => $fromNumber
                    ),
                )
            );
        } else {
            $result = $this->client->tickets()->create(
                array(
                    'subject' => "[WhatsApp] $message",
                    'comment' => array (
                        'body'      => $message,
                        'author_id' => $ticket->submitter_id
                    ),
                    'brand_id' => $this->brandId,
                    'priority' => 'normal'
                )
            );
        }

        return $result;
    }

    private function hasOneOpen($requester)
    {
        $response = $this->client->search(array(
            'query' => "status<solved requester:$requester type:ticket"
        ));

        if ($response->count > 0) {
            return array_shift($response->results);
        } else {
            return false;
        }
    }
}
