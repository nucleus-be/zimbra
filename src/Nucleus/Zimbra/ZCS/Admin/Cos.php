<?php
/**
 * Admin class to query the ZCS api for COS related requests.
 *
 * @author Chris Ramakers <chris@nucleus.be>
 * @license http://www.gnu.org/licenses/gpl.txt
 */
namespace Zimbra\ZCS\Admin;

class Cos
    extends \Zimbra\ZCS\Admin
{

    /**
     * Fetches all Classes of Service from the soap webservice and returns them as an array
     * containing \Zimbra\ZCS\Entity\Cos objects
     * @return array
     */
    public function getCosList()
    {
        $cosList = $this->soapClient->request('GetAllCosRequest')->children()->GetAllCosResponse->cos;

        $results = array();
        foreach ($cosList as $cos) {
            $results[] = \Zimbra\ZCS\Entity\Cos::createFromXml($cos);
        }
        return $results;
    }

    /**
     * Fetches a single class of service from the webservice and returns it
     * as a \Zimbra\ZCS\Entity\Cos object
     * @param string $cos
     * @return \Zimbra\ZCS\Entity\Cos
     */
    public function getCos($cos)
    {
        $params = array(
            'cos' => array(
                '_'  => $cos,
                'by' => 'id',
            )
        );

        $response = $this->soapClient->request('GetCosRequest', array(), $params);
        $coslist = $response->children()->GetCosResponse->children()->cos;

        return \Zimbra\ZCS\Entity\Cos::createFromXml($coslist);
    }

}
