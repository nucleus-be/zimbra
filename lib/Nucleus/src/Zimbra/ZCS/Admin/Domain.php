<?php
/**
 * Zimbra SOAP API calls.
 *
 * @author Chris Ramakers <chris.ramakers@gmail.com>
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
            $results[] = new \Zimbra\ZCS\Entity\Domain ($domain);
        }
        return $results;
    }

    /**
     * Fetches a single domain from the webservice and returns it
     * as a \Zimbra\ZCS\Entity\Domain object
     * @param string $domain
     * @param string $by
     * @param array $attrs
     * @return \Zimbra\ZCS\Entity\Domain
     */
    public function getDomain($domain, $by = 'id', $attrs = array())
    {
        $attributes = array();
        if (!empty($attrs)) {
            $attributes['attrs'] = implode(',', $attrs);
        }

        $params = array(
            'domain' => array(
                '_'  => $domain,
                'by' => $by,
            )
        );

        $response = $this->soapClient->request('GetDomainRequest', $attributes, $params);
        $domains = $response->children()->GetDomainResponse->children();

        return new \Zimbra\ZCS\Entity\Domain($domains[0]);
    }

    public function createDomain($domain)
    {
        $attributes = array();
        $response = $this->soapClient->request('CreateDomainRequest', $attributes, $domain);

        $domain = $response->children()->CreateDomainResponse->children();
        return new \Zimbra\ZCS\Entity\Domain($domain[0]);
    }

    public function deleteDomain($domain_id)
    {
        $attributes = array();
        $response = $this->soapClient->request('DeleteDomainRequest', $attributes, array(
            'id' => $domain_id
        ));

        return true;
    }

}
