<?php

class SiteController {

    /**
    * @param $app Application
    */
    public function getIndex($app) {
        $app->render('index', [
            'title' => 'Hello!',
            'something' => "This is something..." ]);
    }

    public function getInfo($app) {
        phpinfo();
    }

    public function getEcho($app, $msg1, $msg2) {
        $app->render('index', [ 'title' => $msg1, 'something' => $msg2 ]);
    }

    public function postSomething($app) {
        $app->render('index', [ 'title' => 'POST', 'something' => "Something posted ;)" ]);
    }

    public function getTestapi($app) {
        $result = [ "abcde" => "zyx" ];
        return [ 200, $result ];
    }
}
