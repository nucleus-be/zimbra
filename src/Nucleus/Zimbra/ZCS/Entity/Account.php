<?php

/**
 * A Zimbra account.
 *
 * @author Chris Ramakers <chris@nucleus.be>
 * @license http://www.gnu.org/licenses/gpl.txt
 */

namespace Zimbra\ZCS\Entity;

use Symfony\Component\Validator\Mapping\ClassMetadata;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\ExecutionContext;

class Account extends \Zimbra\ZCS\Entity
{
    /**
     * The name of this account
     * @property
     * @var String
     */
    private $name;

    /**
     * The display name of this account
     * @property
     * @var String
     */
    private $displayname;

    /**
     * The mailquota of this account
     * @property
     * @var integer
     */
    private $mailquota;

    /**
     * The status of this account
     * @property
     * @var string
     */
    private $accountstatus = "active";

    /**
     * The username
     * @property
     * @var string
     */
    private $username;

    /**
     * The password for this account
     * @property
     * @var string
     */
    private $password;

    /**
     * The host for the account AKA domain
     * @property
     * @var string
     */
    private $host;

    /**
     * Extra field mapping
     * @var array
     */
    protected static $_datamap = array(
        '@name' => 'name', // @ prefix indicates an attribute
        'displayName' => 'displayname',
        'uid' => 'username',
        'userPassword' => 'password',
        'zimbraMailHost' => 'host',
        'zimbraAccountStatus' => 'accountstatus',
        'zimbraMailQuota' => 'mailquota'
    );

    /**
     * Validation for the properties of this Entity
     *
     * @static
     * @param \Symfony\Component\Validator\Mapping\ClassMetadata $metadata
     */
    static public function loadValidatorMetadata(ClassMetadata $metadata)
    {
        // the actual emailaddress should never be NULL or a blank string
        $metadata->addPropertyConstraint('name', new Assert\NotNull(array(
            'groups' => array('create')
        )));
        $metadata->addPropertyConstraint('name', new Assert\NotBlank(array(
            'groups' => array('create')
        )));
        $metadata->addPropertyConstraint('name', new Assert\MaxLength(array(
            'groups' => array('create'),
            'limit'  => 64
        )));

        // password should never be NULL or a blank string
        $metadata->addPropertyConstraint('password', new Assert\NotNull(array(
            'groups' => array('create')
        )));
        $metadata->addPropertyConstraint('password', new Assert\NotBlank(array(
            'groups' => array('create')
        )));

        // display name max length
        $metadata->addPropertyConstraint('displayname', new Assert\MaxLength(array(
            'groups' => array('create', 'update'),
            'limit' => 250
        )));

        // mailquota numeric and > 0
        $metadata->addPropertyConstraint('mailquota', new Assert\Type(array(
            'groups' => array('create', 'update'),
            'type' => 'integer'
        )));
        $metadata->addPropertyConstraint('mailquota', new Assert\Min(array(
            'groups' => array('create', 'update'),
            'limit' => 0
        )));

        // Account status has fixed set of options and is required
        $metadata->addPropertyConstraint('accountstatus', new Assert\Choice(array(
            'groups' => array('create', 'update'),
            'choices' => array('active', 'closed', 'locked', 'pending', 'maintenance')
        )));
        $metadata->addPropertyConstraint('accountstatus', new Assert\NotNull(array(
            'groups' => array('create', 'update')
        )));

    }

    /**
     * @param String $name
     * @return \Zimbra\ZCS\Entity\Account
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
     * @param String $displayname
     * @return \Zimbra\ZCS\Entity\Account
     */
    public function setDisplayname($displayname)
    {
        $this->displayname = $displayname;
        return $this;
    }

    /**
     * @return String
     */
    public function getDisplayname()
    {
        return $this->displayname;
    }

    /**
     * @param int $mailquota
     * @return \Zimbra\ZCS\Entity\Account
     */
    public function setMailquota($mailquota)
    {
        $this->mailquota = $mailquota;
        return $this;
    }

    /**
     * @return int
     */
    public function getMailquota()
    {
        return $this->mailquota;
    }

    /**
     * @param string $accountstatus
     * @return \Zimbra\ZCS\Entity\Account
     */
    public function setAccountstatus($accountstatus)
    {
        $this->accountstatus = $accountstatus;
        return $this;
    }

    /**
     * @return string
     */
    public function getAccountstatus()
    {
        return $this->accountstatus;
    }

    /**
     * @param string $username
     * @return \Zimbra\ZCS\Entity\Account
     */
    public function setUsername($username)
    {
        $this->username = $username;
        return $this;
    }

    /**
     * @return string
     */
    public function getUsername()
    {
        return $this->username;
    }

    /**
     * @param string $password
     * @return \Zimbra\ZCS\Entity\Account
     */
    public function setPassword($password)
    {
        $this->password = $password;
        return $this;
    }

    /**
     * @return string
     */
    public function getPassword()
    {
        return $this->password;
    }

    /**
     * @param string $host
     * @return \Zimbra\ZCS\Entity\Account
     */
    public function setHost($host)
    {
        $this->host = $host;
        return $this;
    }

    /**
     * @return string
     */
    public function getHost()
    {
        return $this->host;
    }
}
