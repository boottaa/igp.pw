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

class IndexController extends AbstractActionController
{
    private $sql;
    private $salt = 0;
    private $limit = 2;

    function __construct(Sql $sql)
    {
        $this->sql = $sql;
    }

    private function getLink($new){
        $sql = $this->sql;
        $select = $sql->select('links');
        $select->where(['new' => $new]);

        $statement = $sql->prepareStatementForSqlObject($select);
        $r = $statement->execute();

        return $r->current();
    }

    private function getNewLinck(string $source): string
    {
        $new_link = substr(md5($source.$this->salt), 0, $this->limit);
        $checkExistsLink = $this->getLink($new_link);
        
        if(!empty($checkExistsLink)){
            //Если существует то возврощаем ее
            if($checkExistsLink['source'] == $source){
                return $checkExistsLink['new'];
            }

            if ($this->salt <= $this->limit * 5) {
                $this->salt++;
            } elseif ($this->salt > $this->limit * 5) {
                $this->salt = 0;
                $this->limit++;
            }
            $new_link = $this->getNewLinck($source);
        }

        $data = [
            'source' => $source,
            "new" => $new_link,
        ];

        $sql = $this->sql;
        $insert = $sql->insert('links');
        $insert->values($data);

        $statement = $sql->prepareStatementForSqlObject($insert);
        $statement->execute();

        return $new_link;
    }

    public function indexAction()
    {
        $id = $this->params()->fromRoute('id');

        if ($this->getRequest()->isGet() && !empty($id)) {
            $source = $this->getLink($id)['source'];
            if(!empty($source)){
                header('Location: '.$source);
            }
        }

        if ($this->getRequest()->isPost()) {
            try {
                $source = $this->params()->fromPost('source');
                if (filter_var($source, FILTER_VALIDATE_URL) && !preg_match("/(http|https):\/\/igp.pw.*/x", $source)) {
                    $host = "https://".$_SERVER['HTTP_HOST']."/";
                    $newLink = $host.$this->getNewLinck($source);
                }else{
                    $newLink = "Некорректные данные";
                }
//                echo "<h3>{$host}{$newLink}</h3>";
            } catch (\Exception $e) {
//                echo __FILE__ . "<hr /><pre>";
//                print_r($e->getMessage());
//                die();
            }
        }

        $this->layout()->setVariable("newlink", $newLink ?? '');

        return new ViewModel();
    }
}
