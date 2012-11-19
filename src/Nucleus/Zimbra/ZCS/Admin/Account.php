<?php
/**
 * Admin class to query the ZCS api for Account related requests.
 *
 * @author Chris Ramakers <chris@nucleus.be>
 * @license http://www.gnu.org/licenses/gpl.txt
 */
namespace Zimbra\ZCS\Admin;

class Account
    extends \Zimbra\ZCS\Admin
{
    /**
     * Fetches a single account from the webservice and returns it
     * as a \Zimbra\ZCS\Entity\Account object
     * @param string $account
     * @return \Zimbra\ZCS\Entity\Account
     */
    public function getAccount($account)
    {
        $params = array(
            'account' => array(
                '_'  => $account,
                'by' => 'id',
            )
        );

        $response = $this->soapClient->request('GetAccountRequest', array(), $params);
        $accounts = $response->children()->GetAccountResponse->children();

        return \Zimbra\ZCS\Entity\Account::createFromXml($accounts[0]);
    }

    /**
     * Fetches all accounts from the soap webservice and returns them as an array
     * containing \Zimbra\ZCS\Entity\Account objects
     * @param string $domain_name The name of the domain you are looking things up for
     * @return array
     */
    public function getAccountListByDomain($domain_name)
    {
        $attributes = array(
            'domain' => $domain_name,
            'applyCos' => 1,
            'types' => 'accounts'
        );
        $params = array(
            'query' => '!(uid=galsync)' // Exclude the galsync account for each domain
        );

        $response = $this->soapClient->request('SearchDirectoryRequest', $attributes, $params);
        $accountList = $response->children()->SearchDirectoryResponse->children();

        $results = array();
        foreach ($accountList as $account) {
            $results[] = \Zimbra\ZCS\Entity\Account::createFromXml($account);
        }

        return $results;
    }

    /**
     * Fetches all accounts from the soap webservice and returns them as an array
     * containing \Zimbra\ZCS\Entity\Account objects
     * @return array
     */
    public function getAccountList()
    {
        $attributes = array(
            'applyCos' => 1,
            'types' => 'accounts'
        );
        $params = array(
            'query' => '!(uid=galsync)' // Exclude the galsync account for each domain
        );

        $response = $this->soapClient->request('SearchDirectoryRequest', $attributes, $params);
        $accountList = $response->children()->SearchDirectoryResponse->children();

        $results = array();
        foreach ($accountList as $account) {
            $results[] = \Zimbra\ZCS\Entity\Account::createFromXml($account);
        }

        return $results;
    }

    /**
     * Searches the whole account directory for a matching account. Searches on mail address
     * and aliasses. You can 
     * @param string $query The search query
     * @param string $domain Limit the search to this domain
     * @param boolean $ldapFilter Use the $query param as a full LDAP filter, when this is false (default)
     *                            the $query is used as the matching part for a filter on the mail attribute
     * @return \Zimbra\ZCS\Entity\Account
     */
    public function searchAccountList($query = '', $domain = false, $ldapFilter = false)
    {
        $attributes = array(
            'applyCos' => 1,
            'types' => 'accounts'
        );
        if($domain !== false){
            $attributes['domain'] = $domain;
        }

        $query = $ldapFilter ? $query : 'mail='.$query.'';
        $params = array(
            'query' => htmlspecialchars($query, ENT_QUOTES) 
        );

        $response = $this->soapClient->request('SearchDirectoryRequest', $attributes, $params);
        $accountList = $response->children()->SearchDirectoryResponse->children();

        $results = array();
        foreach ($accountList as $account) {
            $results[] = \Zimbra\ZCS\Entity\Account::createFromXml($account);
        }

        return $results;
    }

    /**
     * Creates a new account in the ZCS soap webservice
     * @param \Zimbra\ZCS\Entity\Account $account
     * @return \Zimbra\ZCS\Entity
     */
    public function createAccount(\Zimbra\ZCS\Entity\Account $account)
    {
        // Domain properties
        $propertyArray = $account->toPropertyArray();
        $name = $propertyArray['@name'];
        $pass = $propertyArray['userPassword'];

        // Do not send these attributes
        unset($propertyArray['zimbraId']);
        unset($propertyArray['@name']);
        unset($propertyArray['cn']);
        unset($propertyArray['uid']);
        unset($propertyArray['userPassword']);
        unset($propertyArray['zimbraMailHost']);

        $properties = array(
            'name'       => $name,
            'password'   => $pass,
            'attributes' => $propertyArray
        );

        $response = $this->soapClient->request('CreateAccountRequest', array(), $properties);

        $account = $response->children()->CreateAccountResponse->children();
        return \Zimbra\ZCS\Entity\Account::createFromXml($account[0]);
    }

    /**
     * Updates an account in the ZCS soap webservice
     * @param \Zimbra\ZCS\Entity\Account $account
     * @return \Zimbra\ZCS\Entity
     */
    public function updateAccount(\Zimbra\ZCS\Entity\Account $account)
    {
        // Account properties
        $propertyArray = $account->toPropertyArray();
        $id = $account->getId();

        // Do not send these attributes
        // The name is immutable and zimbraId shouldn't be sent when updating a domain!
        unset($propertyArray['zimbraId']);
        unset($propertyArray['@name']);
        unset($propertyArray['uid']);
        unset($propertyArray['userPassword']);
        unset($propertyArray['zimbraMailHost']);

        $properties = array(
            'id'         => $id,
            'attributes' => $propertyArray
        );

        $response = $this->soapClient->request('ModifyAccountRequest', array(), $properties);

        $updatedAccount = $response->children()->ModifyAccountResponse->children();
        return \Zimbra\ZCS\Entity\Account::createFromXml($updatedAccount[0]);
    }

    /**
     * Returns the usage limit and current usage for an account identified
     * by the $account_id parameter
     * @param string $account_id
     * @return array
     */
    public function getAccountQuotaUsage($account_id = false)
    {
        // Get the account to see if it exists, if not
        // an exception will be thrown
        $account = $this->getAccount($account_id);
        $domain = $account->getDomain();

        // Fetch the quota usage
        $response = $this->soapClient->request('GetQuotaUsageRequest', array('domain' => $domain));
        $xpathQuery = sprintf("//*[local-name()='account' and @id='%s']", $account_id);
        $record = $response->xpath($xpathQuery);

        return array(
            'limit' => (int)$record[0]['limit'],
            'used'  => (int)$record[0]['used']
        );
    }

    /**
     * Returns the usage limit and current usage for all accounts in a domain
     * @param string $domain The string repretenstation of the domain, not the actual domainId!!
     * @return array
     */
    public function getAllAccountQuotaUsage()
    {
        // Fetch the quota usage
        $response = $this->soapClient->request('GetQuotaUsageRequest');

        // Format and respond
        return $this->_formatQuotaResponse($response);
    }

    /**
     * Returns the usage limit and current usage for all accounts in a domain
     * @param string $domain The string repretenstation of the domain, not the actual domainId!!
     * @return array
     */
    public function getAccountQuotaUsageByDomain($domain)
    {
        $attributes = array(
            'domain' => $domain
        );

        // Fetch the quota usage
        $response = $this->soapClient->request('GetQuotaUsageRequest', $attributes);

        // Format and respond
        return $this->_formatQuotaResponse($response);
    }

    /**
     * Formats a response with multiple accounts' quota details into an array
     * @param  SimpleXMLElement $response The XML response from the server
     * @return array
     */ 
    private function _formatQuotaResponse($response)
    {
        $data = array();
        foreach($response->children()->children() as $account) {
            $attributes = ($account->attributes());
            $data[(string)$attributes['id']] = array(
                'limit' => (int)$attributes['limit'],
                'used' => (int)$attributes['used']
            );
        }
        return $data;
    }

    /**
     * Removes an account from the ZCS webservice
     * @param string $account_id
     * @return bool
     */
    public function deleteAccount($account_id)
    {
        $attributes = array();

        $response = $this->soapClient->request('DeleteAccountRequest', $attributes, array(
            'id' => $account_id
        ));

        return true;
    }

}
