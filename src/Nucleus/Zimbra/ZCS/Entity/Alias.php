<?php

/**
 * An account alias
 *
 * @author Chris Ramakers <chris@nucleus.be>
 * @license http://www.gnu.org/licenses/gpl.txt
 */

namespace Zimbra\ZCS\Entity;

use Symfony\Component\Validator\Mapping\ClassMetadata;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\ExecutionContext;

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
     * Validation for the properties of this Entity
     *
     * @static
     * @param \Symfony\Component\Validator\Mapping\ClassMetadata $metadata
     */
    static public function loadValidatorMetadata(ClassMetadata $metadata)
    {
        // Name should never be NULL or a blank string
        $metadata->addPropertyConstraint('name', new Assert\NotNull());
        $metadata->addPropertyConstraint('name', new Assert\NotBlank());

        // Targetid may not be NULL or a blank string
        $metadata->addPropertyConstraint('targetid', new Assert\NotNull());
        $metadata->addPropertyConstraint('targetid', new Assert\NotBlank());
    }

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
