<?php

namespace WaToZendApp\Controller;

use Lib\Request;
use Lib\ZendeskClient;

class ZendeskController extends BaseController {

    public function generateTicket()
    {
        $client = new ZendeskClient('ng');
        $client = $client->getClient();

        try {
            $newTicket = $client->ticket()->update(
            array(
                'id' => 76,
                "comment" => array(
                    "public" => true,
                    "body"   => "hola ticket",
                    "author_id" => 870236612
                )
            )
            );

            //$newTicket = $client->search(array(
              //  'query' => 'status<solved requester:5491161142881@s.whatsapp.net type:ticket'
            //));

            $this->render($newTicket);
        } catch(\Exception $e) {
            var_dump($client->debug());
            var_dump($e);
        }

    }
}
