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

class IndexController extends AbstractActionController
{
    private $sql;
    private $salt = 0;
    private $limit = 2;

    function __construct(Base $sql)
    {
        $this->sql = $sql;
    }

    private function getLink($new){
        try{
            return $this->sql->getOnly(['new' => $new]);
        }catch (\Exception $e){
            return null;
        }
    }

    private function getNewLinck(string $source): string
    {
        $source = urlencode($source);
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

        $this->sql->exchangeArray($data)->save();

        return $new_link;
    }

    public function indexAction()
    {
        $id = $this->params()->fromRoute('id');

        if ($this->getRequest()->isGet() && !empty($id)) {
            $source = $this->getLink($id)['source'];
            if(!empty($source)){
                header('Location: '.urldecode($source));
            }
        }

        if ($this->getRequest()->isPost()) {
            $newLink = '';
            try {
                $source = $this->params()->fromPost('source');
                if (filter_var($source, FILTER_VALIDATE_URL) && !preg_match("/(http|https):\/\/igp.pw.*/x", $source)) {
                    $host = "https://".$_SERVER['HTTP_HOST']."/";
                    $newLink = $host.$this->getNewLinck($source);
                }else{
                    $newLink = "Некорректные данные";
                }
            } catch (\Exception $e) {
                echo "<pre>";
                print_r($e->getMessage());
                die("***DIE***");
            }
            echo $newLink;
            die();
        }

        $this->layout()->setVariable("newlink", $newLink ?? '');

        return new ViewModel();
    }
}
