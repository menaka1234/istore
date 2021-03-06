<?php

/*
 * This file is part of the FOSUserBundle package.
 *
 * (c) FriendsOfSymfony <http://friendsofsymfony.github.com/>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace FOS\UserBundle\Entity;

use FOS\UserBundle\Model\User as AbstractUser;

class User extends AbstractUser
{
    public function __construct()
    {
        trigger_error(sprintf('%s is deprecated. Extend FOS\UserBundle\Model\User directly.', __CLASS__), E_USER_DEPRECATED);
        parent::__construct();
        $this->store_id = 1;
    }
    
    /**
     * @var integer id
     */
    protected $id;
    
    /**
     * @var string
     */
    protected $name;

    /**
     * Set name
     *
     * @param string $name
     * @return User
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Get name
     *
     * @return string 
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Get id
     *
     * @return integer 
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @var integer
     */
    private $store_id;


    /**
     * Set store_id
     *
     * @param integer $storeId
     * @return User
     */
    public function setStoreId($storeId)
    {
        $this->store_id = $storeId;

        return $this;
    }

    /**
     * Get store_id
     *
     * @return integer 
     */
    public function getStoreId()
    {
        return $this->store_id;
    }
}
