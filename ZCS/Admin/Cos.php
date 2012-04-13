<?php
/**
 * Zimbra SOAP API calls.
 *
 * @author Chris Ramakers <chris.ramakers@gmail.com>
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
     * @param string $by
     * @param array $attrs
     * @return \Zimbra\ZCS\Entity\Cos
     */
    public function getCos($cos, $by = 'id', $attrs = array())
    {
        $attributes = array();
        if (!empty($attrs)) {
            $attributes['attrs'] = implode(',', $attrs);
        }

        $params = array(
            'cos' => array(
                '_'  => $cos,
                'by' => $by,
            )
        );

        $response = $this->soapClient->request('GetCosRequest', $attributes, $params);
        $coslist = $response->children()->GetCosResponse->children()->cos;

        return ($coslist);
    }

}
