<?php
/**
 * Zimbra SOAP API calls.
 *
 * @author Chris Ramakers <chris.ramakers@gmail.com>
 */
namespace Zimbra\ZCS\Admin;

class Account
    extends \Zimbra\ZCS\Admin
{
    /**
     * Fetches a single account from the webservice and returns it
     * as a \Zimbra\ZCS\Entity\Account object
     * @param string $account
     * @param string $by
     * @param array $attrs
     * @return \Zimbra\ZCS\Entity\Account
     */
    public function getAccount($account, $by = 'id', $attrs = array())
    {
        $attributes = array();
        if (!empty($attrs)) {
            $attributes['attrs'] = implode(',', $attrs);
        }

        $params = array(
            'account' => array(
                '_'  => $account,
                'by' => $by,
            )
        );

        $response = $this->soapClient->request('GetAccountRequest', $attributes, $params);
        $accounts = $response->children()->GetAccountResponse->children();

        return \Zimbra\ZCS\Entity\Account::createFromXml($accounts[0]);
    }

    /**
     * Fetches all accounts from the soap webservice and returns them as an array
     * containing \Zimbra\ZCS\Entity\Cos objects
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
