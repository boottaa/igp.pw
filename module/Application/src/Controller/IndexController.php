<?php
/**
 * @link      http://github.com/zendframework/ZendSkeletonApplication for the canonical source repository
 * @copyright Copyright (c) 2005-2016 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Application\Controller;

use Application\Model\Base;
use Auth\Helpers\IPAPI;
use Auth\Helpers\Session;
use Auth\Helpers\UserInfo;
use Zend\View\Model\ViewModel;

class IndexController extends BaseController
{
    private $salt = 0;
    private $limit = 2;

    private function getLink($new){
        try{
            return $this->links->getOnly(['new' => $new ]);
        }catch (\Exception $e){
            return null;
        }
    }

    private function getNewLinck(string $source): string
    {
        $new_link = substr(md5($source.$this->salt), 0, $this->limit);
        $checkExistsLink = $this->getLink($new_link);

        if(!empty($checkExistsLink)){
            //Если существует то востанавливаем и возврощаем ее
            if($checkExistsLink['source'] == urlencode($source) && $checkExistsLink['user_id'] == $this->getUserId()){
                if($checkExistsLink['is_deleted'] != '0'){
                    $checkExistsLink['is_deleted'] = '0';
                    $this->links->exchangeArray(iterator_to_array($checkExistsLink))->save();
                }
                return $checkExistsLink['new'];
            }
            if ($this->salt <= $this->limit * 5) {
                $this->salt++;
            } elseif ($this->salt > $this->limit * 5) {
                $this->salt = 0;
                $this->limit++;
            }
            return $this->getNewLinck($source);
        }

        $data = [
            'user_id' => $this->getUserId(),
            'source' => urlencode($source),
            'new' => $new_link,
            'is_deleted' => '0'
        ];

        $this->links->exchangeArray($data)->save();

        return $new_link;
    }

    public function getHistorys()
    {
        $userId = $this->getUserId();
        if ($userId != 0) {
            try {
                $items = iterator_to_array($this->links->fetchAll(['user_id' => $userId]));
                array_walk($items, function (&$item) use ($userId) {
                    $item['date_time'] = date('d.m.Y', strtotime($item['date_time']));
                    $item['source'] = urldecode($item['source']);
                    $item['new'] = "https://" . $_SERVER['HTTP_HOST'] . "/" . $item['new'];
                    $item['activity'] = $this->followLinks->getForTableActivity($userId, $item['id']);
                });
                return $items;
            } catch (\Exception $e) {
                return null;
            }
        }
    }

    public function indexAction()
    {
        $id = $this->params()->fromRoute('id');

        if ($this->getRequest()->isGet() && !empty($id)) {
            $l = $this->getLink($id);
            $source = $l['source'];
            if(!empty($source)){
                $user_ip = UserInfo::getReallIpAddr();
                $ipapi = IPAPI::query($user_ip);
                $useragent = $_SERVER['HTTP_USER_AGENT'];
                
                try{
                    $data = iterator_to_array($this->followLinks->getOnly([
                        'link_id' => $l['id'],
                        'user_ip'  => $user_ip,
                    ]));
                    $data['count']++;
                }catch (\Exception $e){
                    $data = [
                        'link_id' => $l['id'],
                        'user_ip'  => $user_ip,
                        'country' => $ipapi->country,
                        'city' => $ipapi->city,
                        'code_region' => mb_strtolower($ipapi->countryCode),
                        'date_time' => null,
                        'count' => '0',
                        'user_agent' => $useragent,
                        'is_mobile' => preg_match('/(android|bb\d+|meego).+mobile|avantgo|bada\/|blackberry|blazer|compal|elaine|fennec|hiptop|iemobile|ip(hone|od)|iris|kindle|lge |maemo|midp|mmp|netfront|opera m(ob|in)i|palm( os)?|phone|p(ixi|re)\/|plucker|pocket|psp|series(4|6)0|symbian|treo|up\.(browser|link)|vodafone|wap|windows (ce|phone)|xda|xiino/i',$useragent)||preg_match('/1207|6310|6590|3gso|4thp|50[1-6]i|770s|802s|a wa|abac|ac(er|oo|s\-)|ai(ko|rn)|al(av|ca|co)|amoi|an(ex|ny|yw)|aptu|ar(ch|go)|as(te|us)|attw|au(di|\-m|r |s )|avan|be(ck|ll|nq)|bi(lb|rd)|bl(ac|az)|br(e|v)w|bumb|bw\-(n|u)|c55\/|capi|ccwa|cdm\-|cell|chtm|cldc|cmd\-|co(mp|nd)|craw|da(it|ll|ng)|dbte|dc\-s|devi|dica|dmob|do(c|p)o|ds(12|\-d)|el(49|ai)|em(l2|ul)|er(ic|k0)|esl8|ez([4-7]0|os|wa|ze)|fetc|fly(\-|_)|g1 u|g560|gene|gf\-5|g\-mo|go(\.w|od)|gr(ad|un)|haie|hcit|hd\-(m|p|t)|hei\-|hi(pt|ta)|hp( i|ip)|hs\-c|ht(c(\-| |_|a|g|p|s|t)|tp)|hu(aw|tc)|i\-(20|go|ma)|i230|iac( |\-|\/)|ibro|idea|ig01|ikom|im1k|inno|ipaq|iris|ja(t|v)a|jbro|jemu|jigs|kddi|keji|kgt( |\/)|klon|kpt |kwc\-|kyo(c|k)|le(no|xi)|lg( g|\/(k|l|u)|50|54|\-[a-w])|libw|lynx|m1\-w|m3ga|m50\/|ma(te|ui|xo)|mc(01|21|ca)|m\-cr|me(rc|ri)|mi(o8|oa|ts)|mmef|mo(01|02|bi|de|do|t(\-| |o|v)|zz)|mt(50|p1|v )|mwbp|mywa|n10[0-2]|n20[2-3]|n30(0|2)|n50(0|2|5)|n7(0(0|1)|10)|ne((c|m)\-|on|tf|wf|wg|wt)|nok(6|i)|nzph|o2im|op(ti|wv)|oran|owg1|p800|pan(a|d|t)|pdxg|pg(13|\-([1-8]|c))|phil|pire|pl(ay|uc)|pn\-2|po(ck|rt|se)|prox|psio|pt\-g|qa\-a|qc(07|12|21|32|60|\-[2-7]|i\-)|qtek|r380|r600|raks|rim9|ro(ve|zo)|s55\/|sa(ge|ma|mm|ms|ny|va)|sc(01|h\-|oo|p\-)|sdk\/|se(c(\-|0|1)|47|mc|nd|ri)|sgh\-|shar|sie(\-|m)|sk\-0|sl(45|id)|sm(al|ar|b3|it|t5)|so(ft|ny)|sp(01|h\-|v\-|v )|sy(01|mb)|t2(18|50)|t6(00|10|18)|ta(gt|lk)|tcl\-|tdg\-|tel(i|m)|tim\-|t\-mo|to(pl|sh)|ts(70|m\-|m3|m5)|tx\-9|up(\.b|g1|si)|utst|v400|v750|veri|vi(rg|te)|vk(40|5[0-3]|\-v)|vm40|voda|vulc|vx(52|53|60|61|70|80|81|83|85|98)|w3c(\-| )|webc|whit|wi(g |nc|nw)|wmlb|wonu|x700|yas\-|your|zeto|zte\-/i',substr($useragent,0,4)) ? '1' : '0',
                        'platform' => UserInfo::getOS($useragent),
                        'browser' => UserInfo::getBrowser($useragent),
                    ];
                }

                $this->followLinks->exchangeArray($data)->save();
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

        return new ViewModel(["historys" =>  $this->getHistorys()]);
    }



}
