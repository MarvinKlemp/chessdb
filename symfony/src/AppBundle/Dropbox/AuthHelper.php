<?php


namespace AppBundle\Dropbox;

use Dropbox\AppInfo;
use Dropbox\ValueStore;
use Dropbox\WebAuth;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Routing\RouterInterface;

class AuthHelper
{
    /** @var  RouterInterface */
    private $routing;

    /** @var  AppInfo */
    private $appInfo;

    /** @var ValueStore */
    private $valueStorage;

    /** @var string */
    private $clientIdentifier;

    /** @var string */
    private $redirectRouteName;

    /** @var TokenStorage */
    private $tokenStorage;

    /** @var WebAuth */
    private $webAuth;

    /**
     * AuthHelper constructor.
     * @param RouterInterface $routing
     * @param AppInfo $appInfo
     * @param ValueStore $valueStorage
     * @param string $clientIdentifier
     * @param string $redirectRouteName
     * @param TokenStorage $tokenStorage
     */
    public function __construct(
        RouterInterface $routing,
        AppInfo $appInfo,
        ValueStore $valueStorage,
        $clientIdentifier,
        $redirectRouteName,
        TokenStorage $tokenStorage
    ) {
        $this->routing = $routing;
        $this->appInfo = $appInfo;
        $this->valueStorage = $valueStorage;
        $this->clientIdentifier = $clientIdentifier;
        $this->redirectRouteName = $redirectRouteName;
        $this->tokenStorage = $tokenStorage;
    }

    public function getStartUrl()
    {
        return $this->getWebAuth()->start();
    }

    public function finish(Request $request)
    {
        $params = [
            'state' => $request->query->get('state'),
            'error' => $request->query->get('error'),
            'error_description' => $request->query->get('error_description'),
            'code' => $request->query->get('code'),
        ];

        list($accessToken, $userId, $urlState) = $this->getWebAuth()->finish($params);

        $this->tokenStorage->setToken($accessToken);

    }

    private function getWebAuth()
    {
        if (null === $this->webAuth) {
            $this->webAuth = new WebAuth(
                $this->appInfo,
                $this->clientIdentifier,
                $this->routing->generate($this->redirectRouteName, [], UrlGeneratorInterface::ABSOLUTE_URL),
                $this->valueStorage
            );
        }

        return $this->webAuth;
    }
}
