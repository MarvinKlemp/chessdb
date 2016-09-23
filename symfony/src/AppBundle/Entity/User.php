<?php

namespace AppBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Security\Core\User\UserInterface;
use Ramsey\Uuid\Uuid;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

/**
 * @ORM\Entity(repositoryClass="UserRepository")
 * @ORM\Table(
 *     uniqueConstraints={
 *         @ORM\UniqueConstraint(name="user_username_unique", columns="username"),
 *         @ORM\UniqueConstraint(name="user_email_unique", columns="email_address"),
 *     }
 * )
 * @UniqueEntity("username")
 * @UniqueEntity("emailAddress")
 */
class User implements UserInterface
{
    /**
     * @var Uuid
     *
     * @ORM\Id
     * @ORM\Column(type="uuid")
     * @ORM\GeneratedValue(strategy="CUSTOM")
     * @ORM\CustomIdGenerator(class="Ramsey\Uuid\Doctrine\UuidGenerator")
     */
    private $uuid;

    /**
     * @var string
     *
     * @ORM\Column
     * @Assert\NotBlank(groups={"registration"})
     * @Assert\Length(min=4, max=25, groups={"registration"})
     * @Assert\Regex(pattern="/^[a-z0-9]+$/i", groups={"registration"})
     */
    private $username;

    /**
     * @var string
     *
     * @ORM\Column
     */
    private $password;

    /**
     * @var array
     *
     * @ORM\Column(type="simple_array")
     * @Assert\NotBlank
     */
    private $roles = [];

    /**
     * @var string
     *
     * @ORM\Column
     * @Assert\NotBlank(groups={"registration"})
     * @Assert\Email(groups={"registration"})
     */
    private $emailAddress;

    /**
     * @var Player[]|ArrayCollection
     *
     * @ORM\OneToMany(targetEntity="Player", mappedBy="user")
     */
    private $players = [];

    /**
     * @var string
     *
     * @Assert\NotBlank(groups={"registration"})
     * @Assert\Length(min=6, groups={"registration"})
     */
    private $rawPassword;

    /**
     * @var DropboxInfo
     *
     * @ORM\OneToOne(targetEntity="DropboxInfo", mappedBy="user")
     */
    private $dropboxInfo;

    /**
     * User constructor.
     */
    public function __construct()
    {
        $this->players = new ArrayCollection();
    }


    public static function register($username, $emailAddress, $rawPassword)
    {
        $user = new self();
        $user->setUsername($username);
        $user->setEmailAddress($emailAddress);
        $user->setRawPassword($rawPassword);
        $user->setRoles(['ROLE_PLAYER']);

        return $user;
    }

    public function getRoles()
    {
        return $this->roles;
    }

    public function getPassword()
    {
        return $this->password;
    }

    public function getUsername()
    {
        return $this->username;
    }

    /**
     * @return Uuid
     */
    public function getUuid()
    {
        return $this->uuid;
    }

    /**
     * @return string
     */
    public function getEmailAddress()
    {
        return $this->emailAddress;
    }

    /**
     * @return Player[]
     */
    public function getPlayers()
    {
        return $this->players;
    }

    /**
     * @param string $username
     */
    public function setUsername($username)
    {
        $this->username = $username;
    }

    /**
     * @param string $password
     */
    public function setPassword($password)
    {
        $this->password = $password;
    }

    /**
     * @param string $roles
     */
    public function setRoles($roles)
    {
        foreach ($roles as $role) {
            $this->addRole($role);
        }
    }

    public function addRole($role)
    {
        if (in_array($role, $this->roles)) {
            return;
        }
        $this->roles[] = $role;
    }

    public function removeRole($role)
    {
        if (!in_array($role, $this->roles)) {
            return;
        }
        $this->roles = array_filter($this->roles, function($current) use ($role) {
            return $current !== $role;
        });

    }

    /**
     * @param string $emailAddress
     */
    public function setEmailAddress($emailAddress)
    {
        $this->emailAddress = $emailAddress;
    }

    /**
     * @return string
     */
    public function getRawPassword()
    {
        return $this->rawPassword;
    }

    /**
     * @param string $rawPassword
     */
    public function setRawPassword($rawPassword)
    {
        $this->rawPassword = $rawPassword;
    }

    /**
     * @param Player[] $players
     */
    public function setPlayers($players)
    {
        foreach ($players as $player) {
            $this->addPlayer($player);
        }
    }

    /**
     * @param Player $player
     */
    public function addPlayer(Player $player)
    {
        $player->setUser($this);
        $this->players->add($player);
    }

    /**
     * @param Player $player
     */
    public function removePlayer(Player $player)
    {
        $player->removeUser();
        $this->players->removeElement($player);
    }

    /**
     * @return DropboxInfo
     */
    public function getDropboxInfo()
    {
        return $this->dropboxInfo;
    }

    /**
     * @param DropboxInfo $dropboxInfo
     */
    public function setDropboxInfo($dropboxInfo)
    {
        $this->dropboxInfo = $dropboxInfo;
    }

    public function eraseCredentials()
    {
        $this->rawPassword = '';
    }

    public function getSalt()
    {
        return;
    }
}