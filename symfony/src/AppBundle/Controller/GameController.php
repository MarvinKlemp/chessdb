<?php


namespace AppBundle\Controller;

use AppBundle\Entity\Game;
use AppBundle\User\GameType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Component\HttpFoundation\Request;

/**
 * @Route("/game")
 */
class GameController extends Controller
{
    /**
     * @Route("/list")
     * @Template("game/list.html.twig")
     */
    public function listAction()
    {
        return [
            'games' => $this
                ->getDoctrine()
                ->getRepository(Game::class)
                ->findAll()
        ];
    }

    /**
     * @Route("/show/{uuid}")
     * @Template("game/show.html.twig")
     */
    public function showAction(Game $game)
    {
        return ['game' => $game];
    }

    /**
     * @Route("/edit/{uuid}")
     * @Template("game/edit.html.twig")
     * @Security("has_role('ROLE_ADMIN')")
     */
    public function editAction(Request $request, Game $game)
    {
        $form = $this->createForm(GameType::class, $game);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $this->get('app.form.game_type_handler')->handle($form);

            return $this->redirectToRoute('app_game_show', ['uuid' => $game->getUuid()]);
        }

        return [
            'game' => $game,
            'form' => $form->createView(),
        ];
    }
}