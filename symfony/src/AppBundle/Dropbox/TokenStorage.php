<?php


namespace AppBundle\Dropbox;

use AppBundle\Entity\DropboxInfoRepository;
use AppBundle\Entity\User;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

class TokenStorage
{
    /** @var DropboxInfoRepository */
    private $dropboxInfoRepository;

    /** @var TokenStorageInterface */
    private $securityContext;

    /**
     * TokenStorage constructor.
     * @param DropboxInfoRepository $dropboxInfoRepository
     * @param TokenStorageInterface $securityContext
     */
    public function __construct(
        DropboxInfoRepository $dropboxInfoRepository,
        TokenStorageInterface $securityContext
    ) {
        $this->dropboxInfoRepository = $dropboxInfoRepository;
        $this->securityContext = $securityContext;
    }

    /**
     * @return string
     * @throws \Exception
     */
    public function getToken()
    {
        return $this
            ->dropboxInfoRepository
            ->findOrCreateOneByUser($this->getUser())
            ->getAccessToken();
    }

    /**
     * @return bool
     * @throws \Exception
     */
    public function hasToken()
    {
        return boolval(
            $this
                ->dropboxInfoRepository
                ->findOrCreateOneByUser($this->getUser())
                ->getAccessToken()
        );
    }

    /**
     * @param string $token
     * @throws \Exception
     */
    public function setToken($token)
    {
        $dropboxInfo = $this
            ->dropboxInfoRepository
            ->findOrCreateOneByUser($this->getUser());

        $dropboxInfo->setAccessToken($token);

        $this
            ->dropboxInfoRepository
            ->save($dropboxInfo);
    }

    /**
     * @return User
     * @throws \Exception
     */
    private function getUser()
    {
        if (!$this->securityContext->getToken()) {
            throw new \Exception('No token available');
        }

        if (!$user = $this->securityContext->getToken()->getUser()) {
            throw new \Exception('Token has no user');
        }

        if (!$user instanceof User) {
            throw new \Exception('current user has to be an apps user');
        }

        return $user;
    }
}