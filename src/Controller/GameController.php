<?php
namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Twig\Environment;

use App\Entity\Jeu;
use App\Entity\Step;
use App\Entity\Player;
use App\Entity\Question;
use App\Entity\Duo;

use App\Repository\QuestionRepository;
use App\Repository\LevelRepository;
use App\Repository\PlayerRepository;
use App\Repository\StepRepository;
use App\Repository\JeuRepository;
use App\Repository\DuoRepository;

use App\Form\DuoType;

use Doctrine\Common\Persistence\ObjectManager;

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
		$steps = array();array_push($steps,0);
		$players = array();

		//représente le niveau de question atteint par les joueurs
		$this->session->set('juste',0);
		$this->session->set('score',0);
		$this->session->set('niveau',0);
		$this->session->set('chance',0);
		$this->session->set('contexte',"jeu");
		
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
				array_push($players,0);
			} else {
				$jeu->addPlayer($nb_players[0]);
				array_shift($nb_players);
				array_push($players, $nb_players[0]->getId());
			}
		}
		$this->session->set('players', $players);

		//sélection des questions bleues
		if(!empty($nb_bleues)){
			for ($i = 1; $i < 4 ; $i++) {
				$step = new Step($nb_bleues[random_int(0, count($nb_bleues)-1)]);
				while ($jeu->getSteps()->contains($step)) {
					$step = new Step($nb_bleues[random_int(0, count($nb_bleues)-1)]);
				}
				$jeu->addStep($step);
				array_push($steps,$i);
			}
		}
		//sélection des questions blanches
		if(!empty($nb_blanches)){		
			for ($i = 4; $i < 6 ; $i++) {
				$step = new Step($nb_blanches[random_int(0, count($nb_blanches)-1)]);
				while ($jeu->getSteps()->contains($step)) {
					$step = new Step($nb_blanches[random_int(0, count($nb_blanches)-1)]);
				}
				$jeu->addStep($step);
				array_push($steps,$i);
			}
		}
		//sélection de la question rouge
		if(!empty($nb_rouges)){
			$step = new Step($nb_rouges[random_int(0, count($nb_rouges)-1)]);
			$jeu->addStep($step);
			array_push($steps,6);
		}

		//sélection de la question banco
		if(!empty($nb_bancos)){
			$step = new Step($nb_bancos[random_int(0, count($nb_bancos)-1)]);
			$jeu->addStep($step);
			array_push($steps,7);			
		}
		//sélection de la question super banco
		if(!empty($nb_supers)){
			$step = new Step($nb_supers[random_int(0, count($nb_supers)-1)]);
			$jeu->addStep($step);
		}

		$em = $this->getDoctrine()->getManager();
		$em->persist($jeu);

		$this->session->set('jeu', $jeu);
		$this->session->set('steps', $steps);

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
	 * @Route("/question", name="question")
	 * @return Response
	 *
	 */
	public function question(LevelRepository $level): Response
	{
		$contexte = $this->session->get('contexte');


		if( $contexte == "pause" ){
			return $this->redirectToRoute('new_game',[],301);
		}else{

			$step = $this->session->get('steps')[0];

			$question = $this->session->get('jeu')->getSteps()[$step];
			$niveau = $question->getQuestion()->getLevel()->getId();
			$this->session->set('niveau',$niveau);
			$this->session->set('gain',$level->find($niveau)->getScore());

			dump($this->session->get('niveau'),$niveau, $this->session->get('steps'), $this->session->get('juste'),$this->session->get('jeu'));

			return $this->render('accueil.html.twig',[
				'players' => $this->session->get('jeu')->getPlayers(),
				'niveau'  => $level->find($niveau),
				'status'  => $level->find($niveau)->getStatus(),
				'score'   => $this->session->get('jeu')->getScore(),
				'banque' => $this->session->get('bank'),
				'reponse' =>['Bonne réponse', 'Mauvaise réponse'],
				'question'=> $question
			]);
		}
	}
	/**
	 * @Route("/reponse/{reponse}", name="reponse")
	 * @return Response
	 * ici on gère les réponses
	 */
	public function reponse($reponse): Response
	{
		$contexte = $this->session->get('contexte');
		$niveau = $this->session->get('niveau');
		$gain = $this->session->get('gain');
		$juste = $this->session->get('juste');
		$steps = $this->session->get('steps');

		if ( $contexte == "pause") {
			return $this->redirectToRoute('new_game',[],301);
		}

		if ( $contexte == "jeu" ){
			// on retire la question
			$step = array_shift($steps);

			//bonnes réponses
			if ($reponse == "good") {
				$this->session->get('jeu')->addScore($gain);
				$this->session->set('juste',$juste+1);
			}
			// mauvaises réponses
			if ($reponse == "bad") {
				array_push($steps,$step);
			}

			$this->session->set('steps', $steps);
			return $this->redirectToRoute('jeu',['reponse' => $reponse ],301);
		}
		
		if( $contexte == "choix" ){
			if ( $reponse == "good" ){// je poursuis
				array_shift($steps);
				$this->session->set('contexte',"jeu");
				return $this->redirectToRoute('question',[],301);
			}

			if ( $reponse == "bad"){//non je préfère conserver mes gains
				$this->session->set('bank',$this->session->get('bank')-$this->session->get('jeu')->getScore());
				$this->session->get('jeu')->addAllScores();
				return $this->redirectToRoute('gains',['gains'=> $this->session->get('jeu')->getScore()],301);
			}
		}

		if( $contexte == "fin" ){//arrêter
			if ( $reponse == "good" ){// nouveau jeu
				$this->session->set('contexte',"pause");
				return $this->redirectToRoute('new_game',[],301);
			}			
			if ( $reponse == "bad"){	
				return $this->redirectToRoute('scores',[],301);
			}
		}
	}
	/**
	 * @Route("/jeu/{reponse}", name="jeu")
	 * on teste si le jeu doit continuer et on comptabilise les points
	 */
	public function jeu($reponse="")
	{
		$em = $this->getDoctrine()->getManager();

		$niveau = $this->session->get('niveau');
		$steps = $this->session->get('steps');

		//oui sauf si aucun jeu n'est lancé ou un jeu terminé
		if( is_null($this->session->get('jeu')) ){
			return $this->redirectToRoute('new_game',[],301);
		}

		if ($niveau < 3) {
			return $this->redirectToRoute('question',[],301);			
		}
		//assez de bonnes réponses ?
		if ($niveau == 3) {

			//est-ce qu'il y a des questions à reprendre ?
			if ( count($steps) > 2 && $this->session->get('chance') == 0) {
				for ($i = 0; $i < 2; $i++) { //on remet les questions bancos à la fin
					$step = array_shift($steps);
					array_push($steps, $step);
				}

				$this->session->set('steps',$steps);
				$this->session->set('niveau',6);
				$this->session->set('chance',1);

				return $this->redirectToRoute('question',[],301);
			}

			if ($this->session->get('juste') < 5){//pas assez de bonnes réponses
	        	return $this->redirectToRoute('pertes',[],301);
			}else{
				$this->session->set('contexte',"choix");

				dump($niveau, $this->session->get('steps'), $this->session->get('juste'),$this->session->get('jeu'), $this->session->get('contexte'));

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
		}

		if ($niveau == 4) {

			if ( $reponse == "good" ){

				$this->session->set('contexte',"choix");

				return $this->render('accueil.html.twig',[
	                'players'=> $this->session->get('jeu')->getPlayers(),
	                'status' => 'warning',
	                'niveau' => "Vous avez gagné 500€ ! Super Banco ?",
	                'score' => '500',
	                'banque' => $this->session->get('bank'),
	                'reponse' =>['Oui', 'Non'],                                     
	                'question'=>['question'=>['question'=>"Si vous gagnez, vous doublez vos gains ?",'answer'=>"Si vous perdez, vous perdez tout."]]                            
	            ]);
	        } else {
	        	return $this->redirectToRoute('pertes',[],301);
	        }

		}

		if ($niveau == 5){

			if ( $reponse == "good" ){

				$this->session->set('bank',$this->session->get('bank')-1000);
				$this->session->get('jeu')->addAllScores(1000);

				$em->flush();
				$this->session->set('contexte',"fin");		
							dump($niveau, $this->session->get('steps'), $this->session->get('juste'),$this->session->get('jeu'), $this->session->get('contexte'));			

				return $this->redirectToRoute('gains',['gains'=> 1000],301);
			}else{
				return $this->redirectToRoute('pertes',[],301);
			}
			
		}
		return $this->redirectToRoute('bug',[],301);
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
	 * @Route("/init_scores", name="init_scores")
	 * @return Response
	 *
	 */
	public function init_scores(PlayerRepository $p, ObjectManager $em): Response
	{
		$players = $p->findAll();
		foreach ($players as $player) {
			$player->setScore(0);
			$em->persist($player);
		}
		$em->flush();

		return $this->redirectToRoute('scores',[],301);
	}
	/**
	 * @Route("/gains/{gains}", name="gains")
	 * @return Response
	 *
	 */
	public function gains($gains, PlayerRepository $p): Response
	{
		$this->session->set('contexte',"fin");

		$em = $this->getDoctrine()->getManager();

		$gains=$gains/2;

		$p->find($this->session->get('jeu')->getPlayers()[0]->getId())->addScore($gains);
		$p->find($this->session->get('jeu')->getPlayers()[1]->getId())->addScore($gains);
		
		$em->flush();

		return $this->render('accueil.html.twig',[
			'players' => $this->session->get('jeu')->getPlayers(),
			'niveau'  => "*** Vous avez gagné chacun ".$gains." € ! ***",
			'status'  => 'warning',
			'score'   => $this->session->get('jeu')->getScore(),
			'banque' => $this->session->get('bank'),
			'reponse' =>['Nouveau Jeu', 'Scores'],
			'question'=>['question'=>['question'=>"Voilà La question ?",'answer'=>"Voici la réponse !"]]
		]);
	}
	/**
	 * @Route("/pertes", name="pertes")
	 * @return Response
	 *
	 */
	public function pertes (){

		//la banque prend ses gains
		$perte = $this->session->get('jeu')->getScore();
		$this->session->set('bank',$this->session->get('bank')+$perte);

		$this->init();

		$this->session->set('contexte',"fin");

		return $this->render('accueil.html.twig',[
			'players'=> $this->session->get('jeu')->getPlayers(),
			'status' => 'info',
			'niveau' => "* Vous n'avez pas suffisemment de bonnes réponses. Vous avez perdu cette partie ! *",
			'score' => 0,
			'banque' => $this->session->get('bank'),
			'reponse' =>['Nouveau Jeu', 'Arrêter'],				
			'question'=>['question'=>['question'=>"Voilà La question ?",'answer'=>"Voici la réponse !"]]
		]);
	}
	/**
	 * @Route("/", name="home")
	 * @return Response
	 *
	 */
	public function index(): Response
	{
		$this->init();

		if( is_null($this->session->get('bank')) ){
			$bank = 0;
			$this->session->set('bank',0);
		}else{
			$bank = $this->session->get('bank');
		}

		dump($this->session->get('niveau'), $this->session->get('steps'), $this->session->get('juste'),$this->session->get('jeu'), $this->session->get('contexte'));

		return $this->render('accueil.html.twig',[
			'status' => 'light',
			'niveau' => 'Cliquez ici pour commencer un nouveau jeu',
			'score' => 0,
			'banque' => $bank,
			'reponse' =>['Bonne réponse', 'Mauvaise réponse'],					
			'question'=>['question'=>['question'=>"Voilà la question ?",'answer'=>"Voici la réponse !"]],
			'players'=>["Joueur 1", "Joueur 2"]
		]);
	}

	/**
	 * @Route("/players", name="players")
	 * @return Response
	 * pour modifier les joueurs sélectionnés
	 */
	public function players(JeuRepository $j, PlayerRepository $p, Request $request): Response
	{
		$em = $this->getDoctrine()->getManager();
		$jeu = $this->session->get('jeu');
		$players = $this->session->get('players');
		$duo = new Duo();
		//$em->persist($p->find($this->session->get('jeu')->getPlayers()[0]));
		//$em->persist($p->find($this->session->get('jeu')->getPlayers()[1]));

		$form = $this->createForm(DuoType::class, $duo);
		$form->handleRequest($request);

		if ($form->isSubmitted() && $form->isValid()) {
			$em->persist($duo);

			$jeu->removePlayers();
			
			$jeu->addPlayer($duo->getPlayer1());
			$jeu->addPlayer($duo->getPlayer2());
			array_push($players,$duo->getPlayer1()->getId());
			array_push($players,$duo->getPlayer2()->getId());

			$this->session->set('players', $players);
			$this->session->set('jeu', $jeu);
			$this->session->set('contexte', "jeu");

			return $this->redirectToRoute('jeu');
		}

		return $this->render('players.html.twig', [
			'banque' => $this->session->get('bank'),			
			'players'=> $this->session->get('jeu')->getPlayers(),			
			'jeu' => $jeu,
			'form' => $form->createView()
		]);
	}

	private function init(){
		$this->session->set('jeu',new Jeu());
		$this->session->set('niveau',0);
		$this->session->set('steps', array());
		$this->session->get('jeu')->addPlayer(new Player());
		$this->session->get('jeu')->addPlayer(new Player());
		$this->session->set('question',0);
		$this->session->set('chance',0);
		$this->session->set('contexte',"pause");
	}

	/**
	 * @Route("/bug", name="bug")
	 * @return Response
	 *
	 */
	public function bug(PlayerRepository $p): Response
	{
		$em = $this->getDoctrine()->getManager();

		//$p->find($this->session->get('jeu')->getPlayers()[0]->getId())->addScore(1000);
		//$p->find($this->session->get('jeu')->getPlayers()[1]->getId())->addScore(1000);
		//dump($p->find($this->session->get('jeu')->getPlayers()[0]->getId()));
		//dump($p->find($this->session->get('jeu')->getPlayers()[1]->getId()));

		//$em->flush();

		return $this->render('bug.html.twig',[
			'banque' => $this->session->get('bank'),			
			'players'=> $this->session->get('jeu')->getPlayers()
		]);
	}
}
?>
