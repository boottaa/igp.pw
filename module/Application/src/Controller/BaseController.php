<?php
/**
 * Created by PhpStorm.
 * User: b.akhmedov
 * Date: 16.11.18
 * Time: 18:10
 */

namespace Application\Controller;

use Application\Model\FollowLinks;
use Application\Model\Links;
use Auth\Helpers\Session;
use Zend\Mvc\Controller\AbstractActionController;

class BaseController extends AbstractActionController{
    /**
     * @var FollowLinks $followLinks
     * @var Links $links
     * @var Session $session
     */
    protected $followLinks;
    protected $links;
    protected $session;
    private $userId = 0;

    function __construct(Links $links, Session $session)
    {
        $this->links = $links;
        $this->followLinks = $links->followLinks();
        $this->session = $session;
    }

    protected function getUserId(){
        if($this->userId == 0){
            $userSession = $this->session->getUserSession();
            if($userSession){
                $this->userId = $userSession['user_id'];
            }
        }
        return $this->userId;
    }
}