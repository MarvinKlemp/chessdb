<?php


namespace AppBundle\Controller;


use AppBundle\Dropbox\AuthHelper;
use Dropbox\Client;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

/**
 * @Route("/import")
 */
class ImportController extends Controller
{
    /**
     * @Route("/")
     * @Template("import/dropbox.html.twig")
     */
    public function dropboxAction(Request $request)
    {
        $tokenStorage = $this->get('app.dropbox.token_storage');

        if (!$tokenStorage->hasToken()) {
            return $this->redirectToRoute('app_import_authstart');
        }

        $clientIdentifier = 'chessdb';

        $client = new Client($tokenStorage->getToken(), $clientIdentifier);

        $metadata = $client->getMetadataWithChildren('/');

        var_dump($metadata);

        return [];
    }

    /**
     * @Route("/clear")
     */
    public function clearAction(Request $request)
    {
        $request->getSession()->clear();

        return $this->redirectToRoute('app_import_dropbox');
    }

    /**
     * @Route("/auth-start")
     */
    public function authStartAction(Request $request)
    {
        $authUrl = $this->getAuthHelper()->getStartUrl();

        return $this->redirect($authUrl);
    }

    /**
     * @Route("/auth-finish")
     */
    public function authFinishAction(Request $request)
    {
        $this->getAuthHelper()->finish($request);

        return $this->redirectToRoute('app_import_dropbox');
    }

    /**
     * @return AuthHelper
     */
    private function getAuthHelper()
    {
        return $this->get('app.dropbox.auth_helper');
    }
}