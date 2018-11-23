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

class IgpController extends BaseController
{
    public function newsAction()
    {
        return new ViewModel();
    }

    public function aboutAction()
    {
        return new ViewModel();
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
