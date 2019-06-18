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
	 * @Route("/init", name="init")
	 * @return Response
	 *
	 */
	public function init(): Response
	{
		session_unset();

		return $this->render('accueil.html.twig',[
			'status' => 'light',
			'niveau' => 'Cliquez ici pour commencer un nouveau jeu',
			'score' => 0,
			'banque' => 0,	
			'reponse' =>['Bonne réponse', 'Mauvaise réponse'],					
			'question'=>['question'=>['question'=>"Voilà la question ?",'answer'=>"Voici la réponse !"]],
			'players'=>["Joueur 1", "Joueur 2"]
		]);
	}
}
?>
