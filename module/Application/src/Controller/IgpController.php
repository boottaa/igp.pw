<?php
/**
 * @link      http://github.com/zendframework/ZendSkeletonApplication for the canonical source repository
 * @copyright Copyright (c) 2005-2016 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Application\Controller;

use Zend\Db\Sql\Sql;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\Mvc\MvcEvent;
use Zend\View\Model\ViewModel;

class IgpController extends AbstractActionController
{
    private $sql;

    function __construct(Sql $sql)
    {
        $this->sql = $sql;
    }

    private function getLinks($where){
        $sql = $this->sql;
        $select = $sql->select('links');
        $select->where($where)->order('date_time DESC');;

        $statement = $sql->prepareStatementForSqlObject($select);
        $r = $statement->execute();
        return $r;
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
        return new ViewModel();
    }
}
