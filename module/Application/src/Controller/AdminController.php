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

class AdminController extends AbstractActionController
{
    private $sql;

    function __construct(Sql $sql)
    {
        $this->sql = $sql;
    }

//    private function getLink($new){
//        $sql = $this->sql;
//        $select = $sql->select('links');
//        $select->where(['new' => $new]);
//
//        $statement = $sql->prepareStatementForSqlObject($select);
//        $r = $statement->execute();
//        return $r->current();
//    }



    public function indexAction()
    {
//        echo "<pre>";
//        print_r("ADMIN");
//        die("***DIE***");

        return new ViewModel();
    }
}
