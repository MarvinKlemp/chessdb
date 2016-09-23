<?php


namespace AppBundle\Entity;

use Ramsey\Uuid\Uuid;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass="DropboxInfoRepository")
 */
class DropboxInfo
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
     * @ORM\Column(type="text", nullable=true)
     */
    private $accessToken;

    /**
     * @var string
     * @ORM\Column()
     */
    private $pgnDirectory;

    /**
     * @var User
     *
     * @ORM\OneToOne(targetEntity="User", inversedBy="dropboxInfo")
     * @ORM\JoinColumn(name="user_uuid", referencedColumnName="uuid")
     */
    private $user;

    public function __construct()
    {
        $this->pgnDirectory = '/';
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
    public function getAccessToken()
    {
        return $this->accessToken;
    }

    /**
     * @param string $accessToken
     */
    public function setAccessToken($accessToken)
    {
        $this->accessToken = $accessToken;
    }

    /**
     * @return string
     */
    public function getPgnDirectory()
    {
        return $this->pgnDirectory;
    }

    /**
     * @param string $pgnDirectory
     */
    public function setPgnDirectory($pgnDirectory)
    {
        $this->pgnDirectory = $pgnDirectory;
    }

    /**
     * @return User
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     * @param User $user
     */
    public function setUser(User $user)
    {
        $this->user = $user;
    }
}