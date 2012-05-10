<?php

/**
 * A Domain.
 *
 * @author Chris Ramakers <chris@nucleus.be>
 * @license http://www.gnu.org/licenses/gpl.txt
 */

namespace Zimbra\ZCS\Entity;

use Symfony\Component\Validator\Mapping\ClassMetadata;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\ExecutionContext;

class Domain extends \Zimbra\ZCS\Entity
{
    /**
     * The Zimbra ID of the default COS for this Domain
     * @property
     * @var string
     */
    private $defaultCosId = null;

    /**
     * The name for this domain
     * @property
     * @var string
     */
    private $name = null;

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

        // DefaultCosId should either be a non-blank string or NULL
        $metadata->addConstraint(new Assert\Callback(array('_validateDefaultCosId')));
    }

    public function _validateDefaultCosId(ExecutionContext $context)
    {
        if(!is_null($this->getDefaultCosId()) && '' == trim($this->getDefaultCosId())){
            $context->addViolationAtPath('defaultCosId', 'defaultCosId must be null or a valid Cos ID', array(), $this->getDefaultCosId());
        }
    }

    /**
     * Extra field mapping
     * @var array
     */
    protected static $_datamap = array(
        'zimbraDomainDefaultCOSId' => 'defaultCosId',
        'zimbraDomainName' => 'name'
    );

    public function setDefaultCosId($defaultCosId)
    {
        $this->defaultCosId = $defaultCosId;
    }

    public function getDefaultCosId()
    {
        return $this->defaultCosId;
    }

    public function setName($name)
    {
        $this->name = $name;
    }

    public function getName()
    {
        return $this->name;
    }
}
