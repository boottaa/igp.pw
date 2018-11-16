<?php
/**
 * Created by PhpStorm.
 * User: bootta
 * Date: 07.03.18
 * Time: 15:00
 */
namespace Application\Model;

use Zend\InputFilter\InputFilter;
use Zend\Paginator\Paginator;

class FollowLinks extends Base
{
    protected $table = 'follow_links';

    protected $data = [
        'link_id' => null,
        'user_ip'  => '0.0.0.0',
        'country' => null,
        'city' => null,
        'date_time' => null,
        'count' => '0',
        'user_agent' => null,
        'is_mobile' => '0',
        'code_region' => null,
    ];

    public function getInputFilter()
    {
        if (!$this->inputFilter) {
            $inputFilter = new InputFilter();

            $inputFilter->add(array(
                'name'     => 'link_id',
                'required' => false,
                'filters'  => array(
                    array('name' => 'Int'),
                ),
            ));
            $inputFilter->add(array(
                'name'     => 'count',
                'required' => false,
                'filters'  => array(
                    array('name' => 'Int'),
                ),
            ));
            $inputFilter->add(array(
                'name'     => 'is_mobile',
                'required' => false,
                'filters'  => array(
                    array('name' => 'Int'),
                ),
            ));
            $inputFilter->add(array(
                'name'     => 'user_ip',
                'required' => false,
                'filters'  => array(
                    array('name' => 'StripTags'),
                    array('name' => 'StringTrim'),
                ),
                'validators' => array(
                    array(
                        'name'    => 'StringLength',
                        'options' => array(
                            'encoding' => 'UTF-8',
                            'min'      => 5,
                            'max'      => 5000,
                        ),
                    ),
                ),
            ));
            $inputFilter->add(array(
                'name'     => 'country',
                'required' => false,
                'filters'  => array(
                    array('name' => 'StripTags'),
                    array('name' => 'StringTrim'),
                ),
                'validators' => array(
                    array(
                        'name'    => 'StringLength',
                        'options' => array(
                            'encoding' => 'UTF-8',
                            'min'      => 1,
                            'max'      => 5000,
                        ),
                    ),
                ),
            ));

            $inputFilter->add(array(
                'name'     => 'city',
                'required' => false,
                'filters'  => array(
                    array('name' => 'StripTags'),
                    array('name' => 'StringTrim'),
                ),
                'validators' => array(
                    array(
                        'name'    => 'StringLength',
                        'options' => array(
                            'encoding' => 'UTF-8',
                            'min'      => 1,
                            'max'      => 5000,
                        ),
                    ),
                ),
            ));

            $this->inputFilter = $inputFilter;
        }

        return $this->inputFilter;
    }


    public function getFollowLinks($user_id = 0, $limit = 100)
    {
        $sql = "SELECT fl.* FROM igp.follow_links fl
        JOIN igp.links l ON fl.link_id = l.id 
        WHERE l.user_id = {$user_id} LIMIT 100";

        return $this->tableGateway->getAdapter()->driver->getConnection()->execute($sql);
    }

    public function getCount($user_id = 0)
    {
        $sql = "SELECT fl.code_region, (count(*) + SUM(fl.count)) as count FROM igp.follow_links fl
        JOIN igp.links l ON fl.link_id = l.id 
        WHERE l.user_id = {$user_id}
        group by fl.code_region";

        return $this->tableGateway->getAdapter()->driver->getConnection()->execute($sql);
    }

    public function getForTableActivity($user_id = 0){
        $sql = "SELECT DATE_FORMAT(fl.date_time, '%Y/%m/%e') as date, (count(*) + SUM(fl.count)) as count FROM igp.follow_links fl
        JOIN igp.links l ON fl.link_id = l.id 
        WHERE l.user_id = {$user_id}
        group by date";

        return $this->tableGateway->getAdapter()->driver->getConnection()->execute($sql);
    }
}