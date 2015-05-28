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
//            $newTicket = $client->tickets()->create(
//                array(
//                    'subject' => "[WhatsApp] asdasdasd",
//                    'comment' => array (
//                        'body' => "asga fgg df a gsd "
//                    ),
//                    "requester" => array(
//                        "name"  => "Fernando Rivas",
//                        "email" => "5491161142881@s.whatsapp.net"
//                    ),
//                    'brand_id' => "350972",
//                    'priority' => 'normal'
//                )
//            );

            $newTicket = $client->search(array(
                'query' => 'status<solved requester:5491161142881@s.whatsapp.net type:ticket'
            ));

            $this->render($newTicket);
        } catch(\Exception $e) {
            var_dump($client->debug());
            var_dump($e);
        }

    }
}
