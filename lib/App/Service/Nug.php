<?php
namespace App\Service;
use Silex\Application;

class Nug
{

    /**
     * Silex app
     * @var Application
     */
    protected $app;

    /**
     * The Zimbra community server admin class
     * @var \Zimbra\ZCS\Admin\Domain
     */
    protected $zimbraDomainAdmin;

    /**
     * The Zimbra community server admin class
     * @var \Zimbra\ZCS\Admin\Cos
     */
    protected $zimbraCosAdmin;

    /**
     * Initializes properties
     * @param $app Appliction
     */
    public function __construct(Application $app)
    {
        $this->app = $app;
    }

    /**
     * Gets an array with all Nug domains
     * @return array
     */
    public function getDomainList()
    {
        $domains = $this->_getZimbraDomainAdmin()->getDomains();

        $domainList = array();
        foreach($domains as $domain){
            $preparedDomain = $this->_prepareDomain($domain);
            $domainList[] = $preparedDomain;
        }
        return $domainList;
    }

    /**
     * Gets a single Nug domain
     * @param string $domain_id
     * @return array
     */
    public function getDomain($domain_id)
    {
        $domain = $this->_getZimbraDomainAdmin()->getDomain($domain_id);
        return  $this->_prepareDomain($domain);
    }

    /**
     * Creates a Domain in the Nug webservice
     * @param array $domain The data to be saved in the array, only certain properties are allowed
     * @return array
     */
    public function createDomain(array $domain)
    {
        $data = $this->_filterValues($domain, array(
            'name'
        ));

        $newDomain = $this->_getZimbraDomainAdmin()->createDomain($data);
        return  $this->_prepareDomain($newDomain);
    }

    /**
     * Deletes a Domain from the Nug webservice
     * @param string $domain_id
     * @return ??
     */
    public function deleteDomain($domain_id)
    {
        $result = $this->_getZimbraDomainAdmin()->deleteDomain($domain_id);
        return $result;
    }

    /**
     * Gets an array with all Nug Classes of Service
     * @return array
     */
    public function getCosList()
    {
        $cosses = $this->_getZimbraCosAdmin()->getCosList();

        $cosList = array();
        foreach($cosses as $cos){
            $preparedCos = $this->_prepareCos($cos);
            $cosList[] = $preparedCos;
        }
        return $cosList;
    }

    /**
     * Gets a single Nug Class of Service
     * @param string $cos_id
     * @return \Zimbra\ZCS\Entity\Co
     */
    public function getCos($cos_id)
    {
        if(!$cos_id){
             return null;
        } else {
            $cos = $this->_getZimbraCosAdmin()->getCos($cos_id);
            return  $this->_prepareCos($cos);
        }
    }

    /**
     * Gets the Zimbra Domain admin from the DI container
     * @return \Zimbra\ZCS\Admin\Domain
     */
    protected function _getZimbraDomainAdmin()
    {
        if (!$this->zimbraDomainAdmin){
            $this->zimbraDomainAdmin = $this->app['zimbra_admin_domain'];
        }
        return $this->zimbraDomainAdmin;
    }

    /**
     * Gets the Zimbra Cos admin from the DI container
     * @return \Zimbra\ZCS\Admin\Cos
     */
    protected function _getZimbraCosAdmin()
    {
        if (!$this->zimbraCosAdmin){
            $this->zimbraCosAdmin = $this->app['zimbra_admin_cos'];
        }
        return $this->zimbraCosAdmin;
    }

    /**
     * Transforms the response object from the Zimbra soap service into a
     * usable array with only the parameters we need
     * @param \Zimbra\ZCS\Entity\Domain $domain
     * @return array
     */
    private function _prepareDomain(\Zimbra\ZCS\Entity\Domain $domain)
    {
        $result = array(
            'id'             => $domain->id,
            'name'           => $domain->name,
            'default_cos_id' => $domain->zimbraDomainDefaultCOSId,
            'subresources'   => array(
                'detail'   => '/nug/domain/'.$domain->id . '/',
                'account_list' => '/nug/domain/'.$domain->id . '/account/'
            )
        );

        if($domain->zimbraDomainDefaultCOSId){
            $result['subresources']['default_cos'] = '/nug/cos/' . $domain->zimbraDomainDefaultCOSId;
        }

        return $result;
    }

    /**
     * Transforms the response object from the Zimbra soap service into a
     * usable array with only the parameters we need
     * @param \Zimbra\ZCS\Entity\Cos $cos
     * @return array
     */
    private function _prepareCos(\Zimbra\ZCS\Entity\Cos $cos)
    {
        $result = array(
            'id'             => $cos->id,
            'name'           => $cos->name
        );
        return $result;
    }

    /**
     * Returns $data with only the allowed keys set, the rest is
     * discarded
     * @param array $data The array to filter
     * @param array $allowedKeys An array with allowed keys for $data
     * @return array
     */
    private function _filterValues(array $data, $allowedKeys = array())
    {
        $keys = array_flip($allowedKeys);
        return array_intersect_key($data, $keys);
    }

}