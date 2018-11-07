<?php
/**
 * Created by PhpStorm.
 * User: b.akhmedov
 * Date: 06.11.18
 * Time: 16:16
 */

namespace Auth\Helpers;

use Zend\Db\TableGateway\TableGateway;

class Session
{

    private $model = null;
    private $user_id = null;

    function __construct(TableGateway $tableGateway)
    {
        $this->model = $tableGateway;
    }

    public function setUserId(Int $id)
    {
        $this->user_id = $id;
    }

    private function getReallIpAddr()
    {
        if (!empty($_SERVER['HTTP_CLIENT_IP']))   //check ip from share internet
        {
            $ip = $_SERVER['HTTP_CLIENT_IP'];
        } elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR']))   //to check ip is pass from proxy
        {
            $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
        } else {
            $ip = $_SERVER['REMOTE_ADDR'];
        }

        return $ip;
    }

    public function start()
    {
        session_start();
        session_regenerate_id();

        $session_id = session_id();

        $this->model->delete(['user_id' => $this->user_id]);

        $data = [
            'user_id' => $this->user_id,
            'session' => $session_id,
            'ip' => $this->getReallIpAddr()
        ];

        $this->model->insert($data);

        return $session_id;
    }

    public function end()
    {
        $this->model->delete(['session' => session_id(), 'ip' => $this->getReallIpAddr()]);
    }

    public function checkSession()
    {
        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }
        $session = $this->model->select(['session' => session_id(), 'ip' => $this->getReallIpAddr()])->current();
        if(!empty($session)){
            session_id($session['session']);

            return true;
        }
        return false;
    }


}