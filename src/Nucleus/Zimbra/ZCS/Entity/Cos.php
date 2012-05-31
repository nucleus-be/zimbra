<?php

/**
 * A Class of Service.
 *
 * @author Chris Ramakers <chris@nucleus.be>
 * @license http://www.gnu.org/licenses/gpl.txt
 */

namespace Zimbra\ZCS\Entity;

use Symfony\Component\Validator\Mapping\ClassMetadata;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\ExecutionContext;

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
    }

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
