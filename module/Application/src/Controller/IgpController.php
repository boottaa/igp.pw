<?php
/**
 * @link      http://github.com/zendframework/ZendSkeletonApplication for the canonical source repository
 * @copyright Copyright (c) 2005-2016 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Application\Controller;

use Application\Model\Base;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;

class IgpController extends AbstractActionController
{
    private $sql;

    function __construct(Base $sql)
    {
        $this->sql = $sql;
    }

    private function getLinks($where){
        try{
            return $this->sql->fetchAll($where);
        }catch (\Exception $e){
            return null;
        }
    }



    public function aboutAction()
    {
        return new ViewModel();
    }

    public function historyAction()
    {
        $items = iterator_to_array($this->getLinks(['user_id' => '0']));

        array_walk($items, function (&$item) {
            $item['date_time'] = date('d.m.Y', strtotime($item['date_time']));
            $item['source'] = urldecode($item['source']);
            $item['new'] = "https://" . $_SERVER['HTTP_HOST'] . "/" . $item['new'];
        });

        return new ViewModel(['items' => $items]);
    }

    public function loginAction()
    {
        $error = $this->params()->fromQuery('error', null);
        return new ViewModel(['error' => $error]);
    }

    public function registrationAction()
    {
        return new ViewModel();
    }
}
