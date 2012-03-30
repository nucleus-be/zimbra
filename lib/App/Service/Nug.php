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
     * @var \Zimbra\ZCS\Admin
     */
    protected $zimbraDomainAdmin;

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
    public function getDomains()
    {
        $domains = $this->_getZimbraDomainAdmin()->getDomains();

        $domainList = array();
        foreach($domains as $domain){
            $preparedDomain = $this->_prepareDomain($domain);
            $preparedDomain['subresources'] = array(
                'detail'   => '/nug/domain/'.$preparedDomain['id'].'/',
                'account_list' => '/nug/domain/'.$preparedDomain['id'].'/account/'
            );
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
     * Transforms the response object from the Zimbra soap service into a
     * usable array with only the parameters we need
     * @param \Zimbra\ZCS\Entity\Domain $domain
     * @return array
     */
    private function _prepareDomain(\Zimbra\ZCS\Entity\Domain $domain)
    {
        //var_dump($domain);
        $domain = array(
            'id'             => $domain->id,
            'name'           => $domain->name,
            'default_cos_id' => $domain->zimbraDomainDefaultCOSId
        );
        return $domain;
    }

}