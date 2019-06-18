<?php
namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Twig\Environment;

use App\Entity\Jeu;
use App\Entity\Step;
use App\Entity\Player;

use App\Repository\QuestionRepository;
use App\Repository\LevelRepository;
use App\Repository\PlayerRepository;

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
	public function new(PlayerRepository $p, QuestionRepository $q): Response
	{
		$jeu = new Jeu();

		//représente le niveau de question atteint par les joueurs
		$this->session->set('step',-1);
		$this->session->set('score',0);

		$nb_bleues = $q->findAllByLevel(1);
		$nb_blanches = $q->findAllByLevel(2);
		$nb_rouges = $q->findAllByLevel(3);
		$nb_bancos = $q->findAllByLevel(4);
		$nb_supers = $q->findAllByLevel(5);
		$nb_players = $p->findAll();
		shuffle($nb_players);

		// sélection des joueurs
		for ($i = 0; $i <2 ; $i++) {
			if(empty($nb_players)){
				$jeu->addPlayer(new Player());
			} else {
				$jeu->addPlayer($nb_players[0]);
				array_shift($nb_players);
			}
		}

		//sélection des questions bleues
		if(!empty($nb_bleues)){
			for ($i = 1; $i < 4 ; $i++) {
				$step = new Step($i,$nb_bleues[random_int(0, count($nb_bleues)-1)]);
				while ($jeu->getSteps()->contains($step)) {
					$step = new Step($i,$nb_bleues[random_int(0, count($nb_bleues)-1)]);
				}
				$jeu->addStep($step);
			}
		}

		//sélection des questions bleues
		if(!empty($nb_bleues)){
			for ($i = 1; $i < 4 ; $i++) {
				$step = new Step($i,$nb_bleues[random_int(0, count($nb_bleues)-1)]);
				while ($jeu->getSteps()->contains($step)) {
					$step = new Step($i,$nb_bleues[random_int(0, count($nb_bleues)-1)]);
				}
				$jeu->addStep($step);
			}
		}
		//sélection des questions blanches
		if(!empty($nb_blanches)){		
			for ($i = 4; $i < 6 ; $i++) {
				$step = new Step($i,$nb_blanches[random_int(0, count($nb_blanches)-1)]);
				while ($jeu->getSteps()->contains($step)) {
					$step = new Step($i,$nb_blanches[random_int(0, count($nb_blanches)-1)]);
				}
				$jeu->addStep($step);
			}
		}
		//sélection de la question rouge
		if(!empty($nb_rouges)){
			$jeu->addStep(new Step(6,$nb_rouges[random_int(0, count($nb_rouges)-1)]));
		}
		//sélection de la question banco
		if(!empty($nb_bancos)){
			$jeu->addStep(new Step(10,$nb_bancos[random_int(0, count($nb_bancos)-1)]));
		}
		//sélection de la question super banco
		if(!empty($nb_supers)){
			$jeu->addStep(new Step(11,$nb_supers[random_int(0, count($nb_supers)-1)]));
		}
		$this->session->set('jeu', $jeu);
		
		return $this->render('accueil.html.twig',[
			'players'=> $this->session->get('jeu')->getPlayers(),
			'status' => 'light',
			'niveau' => 'Le jeu est prêt ! Cliquez ici pour passer aux étapes suivantes',
			'score' => 0,
			'question'=>['question'=>['question'=>"Voilà La question ?",'answer'=>"Voici la réponse !"]]
		]);

	}
	/**
	 * @Route("/question", name="question")
	 * @return Response
	 *
	 */
	public function question(LevelRepository $level): Response
	{
		$step = $this->session->get('step');
		$step++;
		$this->session->set('step',$step);

		if( is_null($this->session->get('jeu')) || $step >= count($this->session->get('jeu')->getSteps())){
			return $this->redirectToRoute('new_game',[],301);
		}

		$niveau = $this->session->get('jeu')->getSteps()[$step]->getQuestion()->getLevel()->getId();

		return $this->render('accueil.html.twig',[
			'players' => $this->session->get('jeu')->getPlayers(),
			'niveau'  => $level->find($niveau),
			'status'  => $level->find($niveau)->getStatus(),
			'score'   => $this->session->get('score'),
			'question'=> $this->session->get('jeu')->getSteps()[$step]
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