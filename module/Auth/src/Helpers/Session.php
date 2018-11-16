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
    private $session = null;

    function __construct(TableGateway $tableGateway)
    {
        $this->model = $tableGateway;
    }

    public function setUserId(Int $id)
    {
        $this->user_id = $id;
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
            'ip' => UserInfo::getReallIpAddr()
        ];
        $this->model->insert($data);
        
        return $session_id;
    }

    public function end()
    {
        $this->session = null;
        $this->model->delete(['session' => session_id(), 'ip' => UserInfo::getReallIpAddr()]);
    }

    public function getUserSession(){
        if(empty($this->session)){
            $this->session = $this->model->select(['session' => session_id(), 'ip' =>  UserInfo::getReallIpAddr()])->current();

            if(empty($this->session)){
                return false;
            }
        }

        return $this->session;
    }

    public function checkSession()
    {
        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }
        $session = $this->getUserSession();

        if($session){
            session_id($session['session']);
            return true;
        }
        return false;
    }


}