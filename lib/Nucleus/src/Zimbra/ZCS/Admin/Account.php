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

}
