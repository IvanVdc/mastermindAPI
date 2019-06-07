<?php

namespace App\Controller;

use App\Entity\Turn;
use App\Utils\Mastermind;
use App\Utils\Player;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Config\Definition\Exception\Exception;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class ApiController extends AbstractController
{
    /**
     * @Route("/", methods={"GET"}, name="get_user_info")
     */
    public function init(Request $request)
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        $user = $this->getUser();
        $em = $this->getDoctrine()->getManager();
        $player = new Player($em, $user);
        $code = 200;
        $response = [
            'code' => $code,
            'error' => false,
            'data' => $player->getHistoric(),
        ];
        return new Response(json_encode($response), $code);
    }

    /**
     * @Route("/new", methods={"POST"}, name="create_new_game")
     */
    public function newGame(Request $request)
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        $user = $this->getUser();
        $em = $this->getDoctrine()->getManager();
        $player = new Player($em, $user);
        if(!$player->canCreateNewGame()){
            $code = 400;
            $response = [
                'code' => $code,
                'error' => true,
                'data' => 'User can not create a new game',
            ];
        }
        else{
            $code = 200;
            $game = $player->CreateNewGame();
            $response = [
                'code' => $code,
                'error' => false,
                'data' => 'Game with id ' . $game->getId() . ' created',
            ];
        }

        return new Response(json_encode($response), $code);
    }

    /**
     * @Route("/play", methods={"POST"}, name="play_guess")
     */
    public function playGuess(Request $request)
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        $user = $this->getUser();
        $em = $this->getDoctrine()->getManager();
        $player = new Player($em, $user);

        try {
            $game = $player->getGame(true);
        }
        catch(Exception $exc){
            $code = 400;
            $response = [
                'code' => $code,
                'error' => true,
                'data' => $exc->getMessage()
            ];
            return new Response(json_encode($response), $code);
        }
        $mastermind = new Mastermind($em, $game);

        if($mastermind->isOver()){
            $code = 400;
            $response = [
                'code' => $code,
                'error' => true,
                'data' => "Error, please create a new game before play a guess"
            ];
            return new Response(json_encode($response), $code);
        }

        $guess_data = $request->request->get("guess");
        if(is_null($guess_data)){
            $code = 400;
            $response = [
                'code' => $code,
                'error' => true,
                'data' => "Error, guess param is avoid"
            ];
            return new Response(json_encode($response), $code);
        }

        $guess = explode(" ", $guess_data);
        $turn = new Turn();
        $turn->setGame($game);
        $turn->setGuess($guess);
        if(!$mastermind->checkGuess($turn)){
            $code = 400;
            $response = [
                'code' => $code,
                'error' => true,
                'data' => 'The guess sent is incorrect'
            ];
        }
        else{
            try {
                $turn = $mastermind->play($turn);
            }
            catch(Exception $exc){
                $code = 400;
                $response = [
                    'code' => $code,
                    'error' => true,
                    'data' => $exc->getMessage()
                ];
                return new Response(json_encode($response), $code);
            }
            $code = 200;
            $response = [
                'code' => $code,
                'error' => false,
                'data' => array(
                    'over' => $mastermind->isOver(),
                    'winner' => $mastermind->playerWin(),
                    'tries' => $mastermind->turnNumber(),
                    'guess' => $turn->getGuess(),
                    'result' => $turn->getResult()
                )
            ];
        }
        return new Response(json_encode($response), $code);
    }

    /**
     * @Route("/game", methods={"GET"}, name="get_game_info")
     */
    public function history(Request $request)
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        $user = $this->getUser();
        $em = $this->getDoctrine()->getManager();
        $player = new Player($em, $user);

        try {
            $game = $player->getGame(true);
        }
        catch(Exception $exc){
            $code = 400;
            $response = [
                'code' => $code,
                'error' => true,
                'data' => $exc->getMessage()
            ];
            return new Response(json_encode($response), $code);
        }
        $mastermind = new Mastermind($em, $game);

        $code = 200;
        $response = [
            'code' => $code,
            'error' => false,
            'data' => $mastermind->getHistoric()
        ];
        return new Response(json_encode($response), $code);
    }
}