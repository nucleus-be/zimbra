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
     * @var integer
     */
    private $defaultCosId = null;

    /**
     * The name for this domain
     * @property
     * @var string
     */
    private $name = null;

    /**
     * The domain status (active, locked, closed, maintenance, suspended)
     * @property
     * @var string
     */
    private $domainStatus = null;
    
    /**
     * The domain quota (in MB)
     * @property
     * @var integer
     */
    private $domainQuota = null;
    
    /**
     * The domain aggregate quota (in MB)
     * @property
     * @var integer
     */
    private $domainAggregateQuota = null;
    
    /**
     * The domain aggregate quota policy (ALLOWSENDRECEIVE or BLOCKSENDRECEIVE or BLOCKSEND)
     * @property
     * @var string
     */
    private $domainAggregateQuotaPolicy = null;
    
    /**
     * The domain aggregate quota warn percent (e.g. 90)
     * @property
     * @var integer
     */
    private $domainAggregateQuotaWarnPercent = null;
    
    /**
     * The domain aggregate quota warn recipient (email address)
     * @property
     * @var string
     */
    private $domainAggregateQuotaWarnEmailRecipient = null;

    /**
     * Validation for the properties of this Entity
     *
     * @static
     * @param \Symfony\Component\Validator\Mapping\ClassMetadata $metadata
     */
    static public function loadValidatorMetadata(ClassMetadata $metadata)
    {
        // Name should never be NULL or a blank string
        $metadata->addPropertyConstraint('name', new Assert\NotNull(array(
            'groups' => array('create')
        )));
        $metadata->addPropertyConstraint('name', new Assert\NotBlank(array(
            'groups' => array('create')
        )));

        // Domain status has fixed set of options and is required
        $metadata->addPropertyConstraint('domainStatus', new Assert\Choice(array(
            'groups' => array('create', 'update'),
            'choices' => array('active', 'closed', 'locked', 'pending', 'maintenance')
        )));
        $metadata->addPropertyConstraint('domainStatus', new Assert\NotNull(array(
            'groups' => array('create')
        )));

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
        'zimbraDomainName' => 'name',
        'zimbraDomainStatus' => 'domainStatus',
        'zimbraMailDomainQuota' => 'domainQuota',
        'zimbraDomainAggregateQuota' => 'domainAggregateQuota',
        'zimbraDomainAggregateQuotaPolicy' => 'domainAggregateQuotaPolicy',
        'zimbraDomainAggregateQuotaWarnPercent' => 'domainAggregateQuotaWarnPercent',
        'zimbraDomainAggregateQuotaWarnEmailRecipient' => 'domainAggregateQuotaWarnEmailRecipient'
    );

    public function setDefaultCosId($defaultCosId)
    {
        $this->defaultCosId = $defaultCosId;
        return $this;
    }

    public function getDefaultCosId()
    {
        return $this->defaultCosId;
    }

    public function setName($name)
    {
        $this->name = $name;
        return $this;
    }

    public function getName()
    {
        return $this->name;
    }

    public function setDomainStatus($status)
    {
        $this->domainStatus = $status;
        return $this;
    }

    public function getDomainStatus()
    {
        return $this->domainStatus;
    }

    public function setDomainQuota($v)
    {
        $this->domainQuota = $v;
        return $this;
    }

    public function getDomainQuota()
    {
        return $this->domainQuota;
    }
    
    public function setDomainAggregateQuota($v)
    {
        $this->domainAggregateQuota = $v;
        return $this;
    }

    public function getDomainAggregateQuota()
    {
        return $this->domainAggregateQuota;
    }
    
    public function setDomainAggregateQuotaPolicy($v)
    {
        $this->domainAggregateQuotaPolicy = $v;
        return $this;
    }

    public function getDomainAggregateQuotaPolicy()
    {
        return $this->domainAggregateQuotaPolicy;
    }
    
    public function setDomainAggregateQuotaWarnPercent($v)
    {
        $this->domainAggregateQuotaWarnPercent = $v;
        return $this;
    }

    public function getDomainAggregateQuotaWarnPercent()
    {
        return $this->domainAggregateQuotaWarnPercent;
    }
    
    public function setDomainAggregateQuotaWarnEmailRecipient($v)
    {
        $this->domainAggregateQuotaWarnEmailRecipient = $v;
        return $this;
    }

    public function getDomainAggregateQuotaWarnEmailRecipient()
    {
        return $this->domainAggregateQuotaWarnEmailRecipient;
    }
}
