<?php
// src/AppBundle/Entity/User.php

namespace AppBundle\Entity;

use FOS\UserBundle\Model\User as BaseUser;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;


/**
 * @ORM\Entity
 * @ORM\HasLifecycleCallbacks()
 * @ORM\Table(name="fos_user")
 */
class User extends BaseUser
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="created", type="datetime")
     */
    private $created;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="updated", type="datetime")
     */
    private $updated;


    /**
     * @ORM\OneToMany(targetEntity="Bucket", mappedBy="owner")
     */
    private $buckets;

    public function __construct()
    {
        parent::__construct();
        $this->buckets = new ArrayCollection();
    }

    /**
     * Add buckets
     *
     * @param \AppBundle\Entity\Bucket $buckets
     * @return User
     */
    public function addBucket(\AppBundle\Entity\Bucket $buckets)
    {
        $this->buckets[] = $buckets;
        return $this;
    }
    /**
     * Remove buckets
     *
     * @param \AppBundle\Entity\Bucket $buckets
     */
    public function removeBucket(\AppBundle\Entity\Bucket $buckets)
    {
        $this->buckets->removeElement($buckets);
    }
    /**
     * Get buckets
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getBuckets()
    {
        return $this->buckets;
    }

    /**
     * Set created
     *
     * @param \DateTime $created
     * @return Owner
     */
    public function setCreated($created)
    {
        $this->created = $created;

        return $this;
    }

    /**
     * Get created
     *
     * @return \DateTime 
     */
    public function getCreated()
    {
        return $this->created;
    }

    /**
     * Set updated
     *
     * @param \DateTime $updated
     * @return Owner
     */
    public function setUpdated($updated)
    {
        $this->updated = $updated;

        return $this;
    }

    /**
     * Get updated
     *
     * @return \DateTime 
     */
    public function getUpdated()
    {
        return $this->updated;
    }


    /**
     * Converts to string
     *
     * @return string String representation of this class
     */
    public function __toString()
    {
        return get_class();
    }

    /**
     * Pre persist event listener
     *
     * @ORM\PrePersist
     */
    public function beforeSave()
    {
        $this->created = new \DateTime('now', new \DateTimeZone('UTC'));
        $this->updated = new \DateTime('now', new \DateTimeZone('UTC'));
    }

    /**
     * Pre update event handler
     * @ORM\PreUpdate
     */
    public function doUpdate()
    {
        $this->updated = new \DateTime('now', new \DateTimeZone('UTC'));
    }
}

