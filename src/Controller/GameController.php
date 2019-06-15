<?php
namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Twig\Environment;

use App\Entity\Jeu;
use App\Entity\Step;

use App\Repository\QuestionRepository;
use App\Repository\LevelRepository;

use Symfony\Component\HttpFoundation\Session\SessionInterface;


class GameController extends AbstractController
{
	private $session;

	public function __construct(SessionInterface $session)
	{
		$this->session = $session;
	}


	/**
	 * @Route("/new", name="new_game")
	 * @return Response
	 *
	 */
	public function new(QuestionRepository $q): Response
	{
		$jeu = new Jeu();

		//représente le niveau de question atteint par les joueurs
		$this->session->set('niveau',0);

		$nb_bleues = $q->findAllByLevel(1);
		$nb_blanches = $q->findAllByLevel(2);
		$nb_rouges = $q->findAllByLevel(3);
		$nb_bancos = $q->findAllByLevel(4);
		$nb_supers = $q->findAllByLevel(5);

		//sélection des questions bleues
		for ($i = 1; $i < 4 ; $i++) {
			$step = new Step($i,$nb_bleues[random_int(0, count($nb_bleues)-1)]);
			while ($jeu->getSteps()->contains($step)) {
				$step = new Step($i,$nb_bleues[random_int(0, count($nb_bleues)-1)]);
			}
			$jeu->addStep($step);
		}
		//sélection des questions blanches
		for ($i = 4; $i < 6 ; $i++) {
			$step = new Step($i,$nb_blanches[random_int(0, count($nb_blanches)-1)]);
			while ($jeu->getSteps()->contains($step)) {
				$step = new Step($i,$nb_blanches[random_int(0, count($nb_blanches)-1)]);
			}
			$jeu->addStep($step);
		}
		//sélection de la question rouge
		$jeu->addStep(new Step(6,$nb_rouges[random_int(0, count($nb_rouges)-1)]));
		//sélection de la question banco
		$jeu->addStep(new Step(10,$nb_bancos[random_int(0, count($nb_bancos)-1)]));
		//sélection de la question super banco

		$jeu->addStep(new Step(11,$nb_supers[random_int(0, count($nb_supers)-1)]));
		
		return $this->render('test.html.twig',[
			'questions'=> $jeu->getSteps(),
		]);
/*
		return $this->render('accueil.html.twig',[
			'players'=>$players,
			'question'=>$question
		]);
		*/
	}
	/**
	 * @Route("/question", name="question")
	 * @return Response
	 *
	 */
	public function question(LevelRepository $level): Response
	{
		$niveau = $this->session->get('niveau');
		$niveau++;
		$this->session->set('niveau',$niveau);

		return $this->render('accueil.html.twig',[
			'bouton' => 'primary',
			'niveau' => $level->find($niveau)
		]);
	}
	/**
	 * @Route("/response", name="response")
	 * @return Response
	 *
	 */
	public function response(): Response
	{
		return $this->render('accueil.html.twig',[
		]);
	}
}
?>