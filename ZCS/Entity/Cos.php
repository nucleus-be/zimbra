<?php

/**
 * A Class of Service.
 *
 * @author Chris Ramakers <chris.ramakers@gmail.com>
 * @license http://www.gnu.org/licenses/gpl.txt
 */

namespace Zimbra\ZCS\Entity;

class Cos extends \Zimbra\ZCS\Entity
{
    /**
     * Extra field mapping
     * @var array
     */
    protected $_datamap = array(
        'cn' => 'name'
    );
}
