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
use Doctrine\Common\Persistence\ObjectManager;

use Symfony\Component\HttpFoundation\Session\SessionInterface;


class GameController extends AbstractController
{
	private $session;
	private $question;

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
			'score'  => 0,
			'banque' => $this->session->get('bank'),
			'reponse' =>['Bonne réponse', 'Mauvaise réponse'],			
			'question'=>['question'=>['question'=>"Voilà La question ?",'answer'=>"Voici la réponse !"]]
		]);

	}
	/**
	 * @Route("/question/{score}", name="question")
	 * @return Response
	 *
	 */
	public function question(ObjectManager $em, LevelRepository $level, $score): Response
	{
		$step = $this->session->get('step');
		$points = $this->session->get('points');
		$question = $this->session->get('question');
		$contexte = $this->session->get('contexte');

		//on enregistre les résultats
		if ($score == "good") {

			if( $contexte == "jeu") {

				$this->session->get('jeu')->addScore($points);

				$this->session->set('question',$question+1);
				// banco ?
				if ($this->session->get('jeu')->getSteps()[$step]->getQuestion()->getLevel()->getId() == 3 ) {

					$this->session->set('contexte',"choix");

					return $this->render('accueil.html.twig',[
						'players'=> $this->session->get('jeu')->getPlayers(),
						'status' => 'warning',
						'niveau' => "Vous avez gagné ! ".$this->session->get('jeu')->getScore()." € ! Banco ?",
						'score' => $this->session->get('jeu')->getScore(),
						'banque' => $this->session->get('bank'),
						'reponse' =>['Oui', 'Non'],					
						'question'=>['question'=>['question'=>"Voilà La question ?",'answer'=>"Voici la réponse !"]]
					]);
				}
				// super ?
				if ($this->session->get('jeu')->getSteps()[$step]->getQuestion()->getLevel()->getId() == 4 ) {

					$this->session->set('contexte',"choix");

					return $this->render('accueil.html.twig',[
						'players'=> $this->session->get('jeu')->getPlayers(),
						'status' => 'warning',
						'niveau' => "Bravo ! Vous avez gagné 500 € Super Banco ?",
						'score' => 500,
						'banque' => $this->session->get('bank'),
						'reponse' =>['Oui', 'Non'],
						'question'=>['question'=>['question'=>"Voilà La question ?",'answer'=>"Voici la réponse !"]]
					]);
				}
				if ($this->session->get('jeu')->getSteps()[$step]->getQuestion()->getLevel()->getId() == 5 ) {

					$this->session->set('bank',$this->session->get('bank')-1000);
					$this->session->get('jeu')->addAllScores(1000);
					$em->flush();
					$em->persist($this->session->get('jeu'));
					$this->session->set('contexte',"fin");					

					dump($this->session->get('jeu'));

					return $this->render('accueil.html.twig',[
						'players'=> $this->session->get('jeu')->getPlayers(),
						'status' => 'warning',
						'niveau' => '*** Vous avez gagné 1000 € ! ***',
						'score'  => 0,
						'banque'  => $this->session->get('bank'),
						'reponse' =>['Nouveau Jeu', 'Arrêter'],				
						'question'=>['question'=>['question'=>"Voilà La question ?",'answer'=>"Voici la réponse !"]]
					]);
				}
			}
			
			if( $contexte == "choix" ){
dump($contexte);
			}

			if( $contexte == "fin" ){
dump($contexte);
			}
		}

		if ($score == "bad") {

			if( $contexte == "jeu") {

				$this->session->get('jeu')->addScore(-$points);
				//on réintroduit la question
				$this->session->get('jeu')->addStep(new Step($step+7,$this->session->get('jeu')->getSteps()[$step]->getQuestion()));
				// banco ?
				if ($this->session->get('jeu')->getSteps()[$step]->getQuestion()->getLevel()->getId() == 3 ) {
					if ($this->session->get('question') < 5){

						$perte = $this->session->get('jeu')->getScore();

						$players = $this->session->get('jeu')->getPlayers();

						$this->session->set('bank',$this->session->get('bank')-$perte);
						$this->session->set('jeu',new Jeu());

						$this->session->get('jeu')->addPlayer($players[0]);
						$this->session->get('jeu')->addPlayer($players[1]);

						$this->session->set('contexte',"fin");

						return $this->render('accueil.html.twig',[
							'players'=> $this->session->get('jeu')->getPlayers(),
							'status' => 'info',
							'niveau' => "* Vous n'avez pas suffisemment de bonnes réponses. Vous avez perdu ! ".-$perte." € *",
							'score' => 0,
							'banque' => $this->session->get('bank'),
							'reponse' =>['Nouveau Jeu', 'Arrêter'],				
							'question'=>['question'=>['question'=>"Voilà La question ?",'answer'=>"Voici la réponse !"]]
						]);
					}
				}
			}
			if( $contexte == "choix" ){//non je préfère conserver mes gains
				dump($contexte);
				$this->session->set('bank',$this->session->get('bank')-$this->session->get('jeu')->getScore());
				$this->session->get('jeu')->addAllScores();
				return $this->redirectToRoute('new_game',[],301);
			}

			if( $contexte == "fin" ){//arrêter
dump($contexte);
				return $this->redirectToRoute('scores',[],301);
			}
		}

		// on prépare la prochaine question
		$step++;
		$this->session->set('step',$step);

		if( is_null($this->session->get('jeu')) || $step >= count($this->session->get('jeu')->getSteps())){
			return $this->redirectToRoute('new_game',[],301);
		}

		$niveau = $this->session->get('jeu')->getSteps()[$step]->getQuestion()->getLevel()->getId();

		$this->session->set('points',$level->find($niveau)->getScore());
		$this->session->set('contexte',"jeu");

		return $this->render('accueil.html.twig',[
			'players' => $this->session->get('jeu')->getPlayers(),
			'niveau'  => $level->find($niveau),
			'status'  => $level->find($niveau)->getStatus(),
			'score'   => $this->session->get('jeu')->getScore(),
			'banque' => $this->session->get('bank'),
			'reponse' =>['Bonne réponse', 'Mauvaise réponse'],
			'question'=> $this->session->get('jeu')->getSteps()[$step]
		]);
	}
	/**
	 * @Route("/scores", name="scores")
	 * @return Response
	 *
	 */
	public function scores(PlayerRepository $p): Response
	{
		$players = $p->findSorted();

		return $this->render('scores.html.twig',[
			'banque' => $this->session->get('bank'),			
			'players'=> $players
		]);
	}
	/**
	 * @Route("/", name="home")
	 * @return Response
	 *
	 */
	public function index(): Response
	{
		return $this->render('accueil.html.twig',[
			'status' => 'light',
			'niveau' => 'Cliquez ici pour commencer un nouveau jeu',
			'score' => 0,
			'banque' => $this->session->get('bank'),
			'reponse' =>['Bonne réponse', 'Mauvaise réponse'],					
			'question'=>['question'=>['question'=>"Voilà la question ?",'answer'=>"Voici la réponse !"]],
			'players'=>["Joueur 1", "Joueur 2"]
		]);
	}
}
?>
