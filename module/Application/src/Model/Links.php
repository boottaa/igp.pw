<?php
/**
 * Created by PhpStorm.
 * User: bootta
 * Date: 07.03.18
 * Time: 15:00
 */
namespace Application\Model;

use Zend\Db\Adapter\AdapterInterface;
use Zend\InputFilter\InputFilter;
use Zend\Log\LoggerInterface;

class Links extends Base
{

    private $followLinks;
    protected $table = 'links';

    protected $data = [
        'id' => null,
        'user_id'  => 0,
        'source' => null,
        'new' => null,
        'date_time' => null
    ];

    public function __construct(AdapterInterface $dbAdapter, LoggerInterface $logger, $isDebug = false)
    {
        $this->followLinks = new FollowLinks($dbAdapter, $logger, $isDebug);
        parent::__construct($dbAdapter, $logger, $isDebug);
    }

    public function followLinks()
    {
        return $this->followLinks;
    }

    public function countUserLinks($user_id = 0)
    {
        $sql = "SELECT count(*) as c FROM  links WHERE user_id={$user_id}";

        return $this->tableGateway->getAdapter()->driver->getConnection()->execute($sql)->current();
    }

    public function getInputFilter()
    {
        if (!$this->inputFilter) {
            $inputFilter = new InputFilter();

            $inputFilter->add(array(
                'name'     => 'id',
                'required' => false,
                'filters'  => array(
                    array('name' => 'Int'),
                ),
            ));
            $inputFilter->add(array(
                'name'     => 'user_id',
                'required' => false,
                'filters'  => array(
                    array('name' => 'Int'),
                ),
            ));
            $inputFilter->add(array(
                'name'     => 'is_deleted',
                'required' => false,
                'filters'  => array(
                    array('name' => 'Int'),
                ),
            ));
            $inputFilter->add(array(
                'name'     => 'source',
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
                'name'     => 'new',
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
}