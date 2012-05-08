<?php
/**
 * Admin class to query the ZCS api for alias related requests.
 *
 * @author Chris Ramakers <chris.ramakers@gmail.com>
 */
namespace Zimbra\ZCS\Admin;

class Alias
    extends \Zimbra\ZCS\Admin
{

    /**
     * Fetches a single alias from the webservice and returns it
     * as a \Zimbra\ZCS\Entity\Alias object
     * @param string $alias_id
     * @return \Zimbra\ZCS\Entity\Alias
     */
    public function getAlias($alias_id)
    {
        $attributes = array(
            'types' => 'aliases'
        );
        $params = array(
            'query' => sprintf('(zimbraId=%s)', $alias_id)
        );

        $response = $this->soapClient->request('SearchDirectoryRequest', $attributes, $params);
        $aliasList = $response->children()->SearchDirectoryResponse->children();

        return \Zimbra\ZCS\Entity\Alias::createFromXml($aliasList[0]);
    }

}
