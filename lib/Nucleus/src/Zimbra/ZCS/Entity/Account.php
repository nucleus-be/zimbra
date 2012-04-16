<?php

/**
 * A Zimbra account.
 *
 * @author Chris Ramakers <chris.ramakers@gmail.com>
 */

namespace Zimbra\ZCS\Entity;

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
     * The mailquote of this account
     * @property
     * @var integer
     */
    private $mailquota;

    /**
     * The status of this account
     * @property
     * @var string
     */
    private $accountstatus;

    /**
     * The username
     * @property
     * @var string
     */
    private $username;

    /**
     * Extra field mapping
     * @var array
     */
    protected static $_datamap = array(
        'cn' => 'name',
        'uid' => 'username',
        'displayName' => 'displayname',
        'zimbraAccountStatus' => 'accountstatus',
        'zimbraMailQuota' => 'mailquota'
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
     */
    public function setUsername($username)
    {
        $this->username = $username;
    }

    /**
     * @return string
     */
    public function getUsername()
    {
        return $this->username;
    }
}
