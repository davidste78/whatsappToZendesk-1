<?php

namespace WaToZendApp\Controller;

use Lib\Request;
use Lib\ZendeskClient;

class ZendeskController extends BaseController {

    const DEFAULT_PAGINATION = 5;

    public function getInfo()
    {
//        $paramas = array('per_page'=> Request::get('per_page', self::DEFAULT_PAGINATION));
//        $zen     = new Zendesk();
//        $results = $zen->getAll($paramas);
//
//        $this->render($results->tickets);
    }

    public function generateTicket()
    {
        $client = new ZendeskClient('ng');
        $newTicket = $client->create();

        $this->render($newTicket);
    }
}
