<?php
/**
 * Admin class to query the ZCS api for domain related requests.
 *
 * @author Chris Ramakers <chris@nucleus.be>
 * @license http://www.gnu.org/licenses/gpl.txt
 */
namespace Zimbra\ZCS\Admin;

class Domain
    extends \Zimbra\ZCS\Admin
{

    /**
     * Fetches all domains from the soap webservice and returns them as an array
     * containing \Zimbra\ZCS\Entity\Domain objects
     * @return array
     */
    public function getDomains()
    {
        $domains = $this->soapClient->request('GetAllDomainsRequest')->children()->GetAllDomainsResponse->children();
        $results = array();
        foreach ($domains as $domain) {
            $results[] = \Zimbra\ZCS\Entity\Domain::createFromXml($domain);
        }
        return $results;
    }

    /**
     * Fetches a single domain from the webservice and returns it
     * as a \Zimbra\ZCS\Entity\Domain object
     * @param string $domain
     * @return \Zimbra\ZCS\Entity\Domain
     */
    public function getDomain($domain, $by = 'id')
    {
        $params = array(
            'domain' => array(
                '_'  => $domain,
                'by' => $by
            )
        );

        $response = $this->soapClient->request('GetDomainRequest', array(), $params);
        $domains = $response->children()->GetDomainResponse->children();

        return \Zimbra\ZCS\Entity\Domain::createFromXml($domains[0]);
    }

    /**
     * Fetches a list of all domain aliasses defined in the system
     * Note that in order to properly link an alias to a domain the zimbraDomainAliasTargetId
     * property must be set in zimbra, else there is no failsafe way of determining what aliasses
     * belong to what domain
     *
     * @return array
     */
    public function getAllDomainAliasses()
    {
        $attributes = array(
            'applyCos' => 1,
            'types' => 'domains'
        );
        $params = array(
            'query' => '(zimbraDomainType=alias)'
        );

        $response = $this->soapClient->request('SearchDirectoryRequest', $attributes, $params);
        $aliasList = $response->children()->SearchDirectoryResponse->children();

        $results = array();
        foreach ($aliasList as $aliasXml) {
            /** @var $alias \Zimbra\ZCS\Entity\Domain\Alias */
            $alias = \Zimbra\ZCS\Entity\Domain\Alias::createFromXml($aliasXml);
            $targetId = $alias->getTargetid();
            if(!$targetId){
                // Todo: log that we have an alias without target id
                continue;
            }
            $domainEntity = $this->getDomain($targetId);
            $alias->setTargetname($domainEntity->getName());
            $results[] = $alias;
        }

        return $results;
    }

    /**
     * Get all aliasses for a given domain which is identified by it's Zimbra ID
     * @param string|\Zimbra\ZCS\Entity\Domain $domain The zimbra id of the domain you are retrieving aliasses for or an instance of the Domain Entity
     * @throws \InvalidArgumentException
     * @return array
     */
    public function getDomainAliasses($domain)
    {
        if($domain instanceof \Zimbra\ZCS\Entity\Domain){
            $domain_id = $domain->getId();
        } elseif(is_string($domain)) {
            $domain_id = $domain;
        } else {
            throw new \InvalidArgumentException(__METHOD__ . ' only accepts the ID of a domain or a Domain Entity');
        }

        $attributes = array(
            'applyCos' => 1,
            'types' => 'domains'
        );
        $params = array(
            'query' => sprintf('(&amp;(zimbraDomainType=alias)(zimbraDomainAliasTargetId=%s))', $domain_id)
        );

        $response = $this->soapClient->request('SearchDirectoryRequest', $attributes, $params);
        $aliasList = $response->children()->SearchDirectoryResponse->children();

        $results = array();
        foreach ($aliasList as $aliasXml) {
            /** @var $alias \Zimbra\ZCS\Entity\Domain\Alias */
            $alias = \Zimbra\ZCS\Entity\Domain\Alias::createFromXml($aliasXml);
            $targetId = $alias->getTargetid();
            if(!$targetId){
                // Todo: log that we have an alias without target id
                continue;
            }
            $domainEntity = $this->getDomain($targetId);
            $alias->setTargetname($domainEntity->getName());
            $results[] = $alias;
        }

        return $results;
    }

    /**
     * Creates a new DomainAlias with name $alias for a given domain
     * @param string|\Zimbra\ZCS\Entity\Domain $domain
     * @param string                           $alias
     * @param null|string                      $description Optionally a description for the item in Zimbra
     * @return \Zimbra\ZCS\Entity\Domain\Alias
     */
    public function createDomainAlias($domain, $alias, $description = null)
    {
        if(is_string($domain)) {
            $domain = $this->getDomain($domain);
        }

        $properties = array(
            'name'       => $alias,
            'attributes' => array(
                'zimbraDomainType' => 'alias',
                'zimbraMailCatchAllForwardingAddress' => "@".$domain->getName(),
                'zimbraMailCatchAllAddress' => "@".$domain->getName(),
                'zimbraDomainAliasTargetId' => $domain->getId(),
                'description' => $description ?: 'domain alias of ' . $domain->getName()
            )
        );

        $response = $this->soapClient->request('CreateDomainRequest', array(), $properties);
        $domainXmlResponse = $response->children()->CreateDomainResponse->children();

        $alias = \Zimbra\ZCS\Entity\Domain\Alias::createFromXml($domainXmlResponse[0]);
        $alias->setTargetname($domain->getName());

        return $alias;
    }

    /**
     * Removes a domain alias from the ZCS webservice
     * @param string|\Zimbra\ZCS\Entity\Domain\Alias $domainAlias
     * @return bool
     */
    public function deleteDomainAlias($domainAlias)
    {
        return $this->deleteDomain($domainAlias, false, false);
    }

    /**
     * Removes all aliasses from a domain
     * @param string|\Zimbra\ZCS\Entity\Domain\Alias $domain
     * @return bool
     * @throws \InvalidArgumentException
     */
    public function deleteAllDomainAliasses($domain)
    {
        if($domain instanceof \Zimbra\ZCS\Entity\Domain){
            $domain_id = $domain->getId();
        } elseif(is_string($domain)) {
            $domain_id = $domain;
            $domain = $this->getDomain($domain_id);
        } else {
            throw new \InvalidArgumentException(__METHOD__ . ' only accepts the ID of a domain or a Domain Entity');
        }

        $aliasses = $this->getDomainAliasses($domain);
        foreach($aliasses as $alias){
            $this->deleteDomainAlias($alias);
        }

        return true;
    }

    /**
     * Removes all accounts from a domain
     * @param string|\Zimbra\ZCS\Entity\Domain\Alias $domain
     * @return bool
     * @throws \InvalidArgumentException
     */
    public function deleteAllDomainAccounts($domain)
    {
        if($domain instanceof \Zimbra\ZCS\Entity\Domain){
            $domain_id = $domain->getId();
        } elseif(is_string($domain)) {
            $domain_id = $domain;
            $domain = $this->getDomain($domain_id);
        } else {
            throw new \InvalidArgumentException(__METHOD__ . ' only accepts the ID of a domain or a Domain Entity');
        }

        $accountAdmin = new \Zimbra\ZCS\Admin\Account($this->getSoapClient());
        $accounts = $accountAdmin->getAccountListByDomain($domain->getName());
        foreach($accounts as $account) {
            $accountAdmin->deleteAccount($account->getId());
        }

        return true;
    }

    /**
     * Creates a domain in the ZCS soap webservice
     * @param \Zimbra\ZCS\Entity\Domain $domain
     * @return \Zimbra\ZCS\Entity
     */
    public function createDomain(\Zimbra\ZCS\Entity\Domain $domain)
    {
        // Domain properties
        $propertyArray = $domain->toPropertyArray();
        $name = $propertyArray['zimbraDomainName'];

        // Do not send a zimbraDomainName or zimbraId attribute
        // The name is sent in the <name> tag and zimbraId shouldn't be sent when creating a new domain!
        unset($propertyArray['zimbraId']);
        unset($propertyArray['zimbraDomainName']);

        $properties = array(
            'name'       => $name,
            'attributes' => $propertyArray
        );

        $response = $this->soapClient->request('CreateDomainRequest', array(), $properties);

        $domain = $response->children()->CreateDomainResponse->children();
        return \Zimbra\ZCS\Entity\Domain::createFromXml($domain[0]);
    }

    /**
     * Updates a domain in the ZCS soap webservice
     * @param \Zimbra\ZCS\Entity\Domain $domain
     * @return \Zimbra\ZCS\Entity
     */
    public function updateDomain(\Zimbra\ZCS\Entity\Domain $domain)
    {
        // Domain properties
        $propertyArray = $domain->toPropertyArray();
        $id = $domain->getId();

        // Do not send a zimbraDomainName or zimbraId attribute
        // The name is immutable and zimbraId shouldn't be sent when updating a domain!
        unset($propertyArray['zimbraId']);
        unset($propertyArray['zimbraDomainName']);

        $properties = array(
            'id'         => $id,
            'attributes' => $propertyArray
        );

        $response = $this->soapClient->request('ModifyDomainRequest', array(), $properties);

        $domain = $response->children()->ModifyDomainResponse->children();
        return \Zimbra\ZCS\Entity\Domain::createFromXml($domain[0]);
    }

    /**
     * Removes a domain from the ZCS webservice
     * @warning This method is also used to delete Domain aliasses since in Zimbra a domain alias is just a Domain of a different type
     * @see \Zimbra\ZCS\Admin\Domain::deleteDomainAlias
     * @param                                  $domain
     * @param bool                             $deleteAliasses
     * @param bool                             $deleteAccounts
     * @throws \InvalidArgumentException
     * @return bool
     */
    public function deleteDomain($domain, $deleteAliasses = true, $deleteAccounts = false)
    {
        if($domain instanceof \Zimbra\ZCS\Entity){
            $domain_id = $domain->getId();
        } elseif(is_string($domain)) {
            $domain_id = $domain;
            $domain = $this->getDomain($domain_id);
        } else {
            throw new \InvalidArgumentException(__METHOD__ . ' only accepts the ID of a domain or a Domain Entity');
        }

        // Remove all accounts from a domain
        if($deleteAccounts == true){
            $this->deleteAllDomainAccounts($domain);
        }

        // Remove all aliasses from a domain
        if($deleteAliasses == true) {
            $this->deleteAllDomainAliasses($domain);
        }

        $response = $this->soapClient->request('DeleteDomainRequest', array(), array(
            'id' => $domain_id
        ));

        return true;
    }

}
