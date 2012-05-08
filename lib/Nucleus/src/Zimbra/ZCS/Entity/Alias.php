<?php

/**
 * An account alias
 *
 * @author Chris Ramakers <chris.ramakers@gmail.com>
 */

namespace Zimbra\ZCS\Entity;

class Alias extends \Zimbra\ZCS\Entity
{
    /**
     * The name of this Alias
     * @property
     * @var String
     */
    private $name;

    /**
     * The uid of this alias (the part prefixing the @ in the name
     * @property
     * @var String
     */
    private $uid;

    /**
     * The target name of this alias (the whole emailaddress that alias points to)
     * @property
     * @var String
     */
    private $targetname;

    /**
     * The id of the alias target
     * @property
     * @var String
     */
    private $targetid;

    /**
     * Extra field mapping
     * @var array
     */
    protected static $_datamap = array(
        '@name'               => 'name',
        'uid'                 => 'uid',
        '@targetName'         => 'targetname',
        'zimbraAliasTargetId' => 'targetid'
    );

    /**
     * @param String $name
     * @return \Zimbra\ZCS\Entity\Alias
     */
    public function setName($name)
    {
        $this->name = $name;
        return $this;
    }

    /**
     * @return String
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param String $targetname
     * @return \Zimbra\ZCS\Entity\Alias
     */
    public function setTargetname($targetname)
    {
        $this->targetname = $targetname;
        return $this;
    }

    /**
     * @return String
     */
    public function getTargetname()
    {
        return $this->targetname;
    }

    /**
     * @param String $uid
     * @return \Zimbra\ZCS\Entity\Alias
     */
    public function setUid($uid)
    {
        $this->uid = $uid;
        return $this;
    }

    /**
     * @return String
     */
    public function getUid()
    {
        return $this->uid;
    }

    /**
     * @param String $targetid
     * @return \Zimbra\ZCS\Entity\Alias
     */
    public function setTargetid($targetid)
    {
        $this->targetid = $targetid;
        return $this;
    }

    /**
     * @return String
     */
    public function getTargetid()
    {
        return $this->targetid;
    }

}
