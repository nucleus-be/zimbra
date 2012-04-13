<?php

/**
 * A Domain.
 *
 * @author Chris Ramakers <chris.ramakers@gmail.com>
 */

namespace Zimbra\ZCS\Entity;

use Symfony\Component\Validator\Mapping\ClassMetadata;
use Symfony\Component\Validator\Constraints as Assert;

class Domain extends \Zimbra\ZCS\Entity
{
    /**
     * The Zimbra ID of the default COS for this Domain
     * @property
     * @var string
     */
    private $defaultCosId;

    /**
     * The name for this domain
     * @property
     * @var string
     */
    private $name;

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
        // TODO: NotBlank does not allow NULL!
        $metadata->addPropertyConstraint('defaultCosId', new Assert\NotBlank());
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
