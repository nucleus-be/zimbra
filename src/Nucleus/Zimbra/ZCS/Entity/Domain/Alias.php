<?php

/**
 * An account alias
 *
 * @author Chris Ramakers <chris@nucleus.be>
 * @license http://www.gnu.org/licenses/gpl.txt
 */

namespace Zimbra\ZCS\Entity\Domain;

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
     * The id of this alias
     * @property
     * @var String
     */
    private $id;

    /**
     * The id of the alias target
     * @property
     * @var String
     */
    private $targetid;

    /**
     * The target name of this alias
     * @property
     * @var String
     */
    private $targetname;

    /**
     * Extra field mapping
     * @var array
     */
    protected static $_datamap = array(
        '@name'                     => 'name',
        '@id'                       => 'id',
        'zimbraDomainAliasTargetId' => 'targetid',

        // dummy property, the target name is never returned from the api but
        // added by the admin classes later
        'targetName'                => 'targetname'
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
     * @param String $id
     * @return \Zimbra\ZCS\Entity\Domain\Alias
     */
    public function setId($id)
    {
        $this->id = $id;
        return $this;
    }

    /**
     * @return String
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param String $targetid
     * @return \Zimbra\ZCS\Entity\Domain\Alias
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

    /**
     * @param String $targetname
     * @return \Zimbra\ZCS\Entity\Domain\Alias
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

}
