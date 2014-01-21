<?php
/**
 * Litus is a project by a group of students from the KU Leuven. The goal is to create
 * various applications to support the IT needs of student unions.
 *
 * @author Niels Avonds <niels.avonds@litus.cc>
 * @author Karsten Daemen <karsten.daemen@litus.cc>
 * @author Koen Certyn <koen.certyn@litus.cc>
 * @author Bram Gotink <bram.gotink@litus.cc>
 * @author Dario Incalza <dario.incalza@litus.cc>
 * @author Pieter Maene <pieter.maene@litus.cc>
 * @author Kristof Mariën <kristof.marien@litus.cc>
 * @author Lars Vierbergen <lars.vierbergen@litus.cc>
 * @author Daan Wendelen <daan.wendelen@litus.cc>
 *
 * @license http://litus.cc/LICENSE
 */

namespace StoreBundle\Entity;

use Doctrine\ORM\Mapping as ORM,
	Doctrine\Common\Collections\ArrayCollection;

/**
 * @ORM\Entity(repositoryClass="StoreBundle\Repository\Store")
 * @ORM\Table(name="store.store")
 * @ORM\InheritanceType("JOINED")
 * @ORM\DiscriminatorColumn(name="inheritance_type", type="string")
 *
 * @author Daan Wendelen <daan.wendelen@litus.cc>
 */
class Store
{
    public function __construct()
    {
        $this->editRoles = new ArrayCollection();
        $this->useRoles = new ArrayCollection();
    }

    /**
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @var integer
     *
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="bigint")
     */
    private $id;

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Factory Only
     *
     * @param string $name
     *
     * @return \StoreBundle\Entity\Store
     */
    public function setName($name)
    {
        $this->name = $name;
        return $this;
    }

    /**
     * @var string The name of this store
     *
     * @ORM\Column(type="string")
     */
    private $name;

    /**
     * @param \CommonBundle\Entity\Acl\Role $role
     * 
     * @return boolean
     */
    public function canBeEditedByRole($role)
    {
        return $this->getEditRoles()->contains($role);
    }

    /**
     * @param \CommonBundle\Entity\Acl\Role $editRole
     * 
     * @return \StoreBundle\Entity\Store
     */
    public function addEditRole($editRole)
    {
        $this->editRoles[] = $editRole;
    }

    /**
     * @return \Doctrine\Common\Collections\ArrayCollection
     */
    protected function getEditRoles()
    {
        return $this->editRoles;
    }

    /**
     * @param \Doctrine\Common\Collections\ArrayCollection $editRoles
     * 
     * @return \StoreBundle\Entity\Store
     */
    protected function setEditRoles($editRoles)
    {
        $this->editRoles = $editRoles;
        return $this;
    }

    /**
     * @var \Doctrine\Common\Collections\ArrayCollection The roles that can edit this store
     *
     * @ORM\ManyToMany(targetEntity="CommonBundle\Entity\Acl\Role")
     * @ORM\JoinTable(
     *      name="store.store_edit_roles",
     *      joinColumns={@ORM\JoinColumn(name="store", referencedColumnName="id")},
     *      inverseJoinColumns={@ORM\JoinColumn(name="role", referencedColumnName="name")}
     * )
     */
    private $editRoles;

    /**
     * @param \CommonBundle\Entity\Acl\Role $role
     * 
     * @return boolean
     */
    public function canUsedByRole($role)
    {
        return $this->getUseRoles->contains($role);
    }

    /**
     * @param \CommonBundle\Entity\Acl\Role $useRole
     * 
     * @return \StoreBundle\Entity\Store
     */
    public function addUseRole($useRole)
    {
        $this->useRole[] = $useRole;
    }

    /**
     * @return \Doctrine\Common\Collections\ArrayCollection
     */
    protected function getUseRoles()
    {
        return $this->useRole;
    }

    /**
     * @param \Doctrine\Common\Collections\ArrayCollection $useRole
     * 
     * @return \StoreBundle\Entity\Store
     */
    protected function setUseRoles($useRole)
    {
        $this->useRole = $useRole;
        return $this;
    }

    /**
     * @var \Doctrine\Common\Collections\ArrayCollection
     *          The roles that can use the store. For example: the whole
     *          fakbarteam can use the fakbar-store.
     *
     * @ORM\ManyToMany(targetEntity="CommonBundle\Entity\Acl\Role")
     * @ORM\JoinTable(
     *      name="store.store_use_roles",
     *      joinColumns={@ORM\JoinColumn(name="store", referencedColumnName="id")},
     *      inverseJoinColumns={@ORM\JoinColumn(name="role", referencedColumnName="name")}
     * )
     */
    private $useRole;

    /**
     * @param \StoreBundle\Entity\Storage $storage
     */
    public function addStorage($storage)
    {
    	$this->storages[] = $storage;
    }
    
    /**
     * @return \Doctrine\Common\Collections\ArrayCollection
     */
    protected function getStorages()
    {
    	return $this->storages;
    }
    
    /**
     * @param \Doctrine\Common\Collections\ArrayCollection $storages
     *
     * @return \StoreBundle\Entity\Store
     */
    protected function setStorages($storages)
    {
    	$this->storages = $storages;
    	return $this;
    }
    
    /**
     * @var \Doctrine\Common\Collections\ArrayCollection
     */
    private $storages;
}
