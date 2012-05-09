<?php

/**
 * A Class of Service.
 *
 * @author Chris Ramakers <chris.ramakers@gmail.com>
 */

namespace Zimbra\ZCS\Entity;

class Cos extends \Zimbra\ZCS\Entity
{
    /**
     * The name of this COS
     * @property
     * @var String
     */
    private $name;

    /**
     * Extra field mapping
     * @var array
     */
    protected static $_datamap = array(
        'cn' => 'name'
    );

    /**
     * @param String $name
     */
    public function setName($name)
    {
        $this->name = $name;
    }

    /**
     * @return String
     */
    public function getName()
    {
        return $this->name;
    }
}
