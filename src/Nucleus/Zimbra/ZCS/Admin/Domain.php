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

        return \Zimbra\ZCS\Entity\Domain::createFromXml($domains[0]);
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
     * @param string $domain_id
     * @return bool
     */
    public function deleteDomain($domain_id)
    {
        $attributes = array();

        $response = $this->soapClient->request('DeleteDomainRequest', $attributes, array(
            'id' => $domain_id
        ));

        return true;
    }

}
