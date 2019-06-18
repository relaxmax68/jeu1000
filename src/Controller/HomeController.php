<?php
namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Twig\Environment;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

class HomeController extends AbstractController
{
	/**
	 * @Route("/", name="home")
	 * @return Response
	 *
	 */
	public function index(): Response
	{
		session_unset();

		return $this->render('accueil.html.twig',[
			'status' => 'light',
			'niveau' => 'Cliquez ici pour commencer un nouveau jeu',
			'score' => 0,
			'question'=>['question'=>['question'=>"Voilà la question ?",'answer'=>"Voici la réponse !"]],
			'players'=>["Joueur 1", "Joueur 2"]
		]);
	}
	/**
	 * @Route("/scores", name="scores")
	 * @return Response
	 *
	 */
	public function scores(): Response
	{
		return $this->render('accueil.html.twig',[
			'status' => 'light',
			'niveau' => 'PAUSE',
			'score' => 0,
			'question'=>['question'=>['question'=>"",'answer'=>""]],
			'players'=>["Joueur 1", "Joueur 2"]
		]);
	}
}
?>
