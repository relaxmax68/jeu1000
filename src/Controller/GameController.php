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
use Symfony\Component\HttpFoundation\Cookie;


class GameController extends AbstractController
{
	private $session;

	public function __construct(SessionInterface $session, PlayerRepository $p,QuestionRepository $q)
	{
		$this->session = $session;
		$this->q = $q;
		$this->p = $p;
	}

	/**
	 * @Route("/new", name="new_game")
	 * @return Response
	 *
	 */
	public function new(): Response
	{
		$this->session->set('juste',0);//nombre de réponses justes
		$this->session->set('score',0);//score atteint par les joueurs
		$this->session->set('niveau',0);//niveau atteint par les joueurs
		$this->session->set('chance',0);//une deuxième chance est donnée à chague question
		$this->session->set('contexte',"jeu");//contexte de jeu : pause / questions / banco
		
		$this->preparation();

		return $this->render('accueil.html.twig',[
			'players'=> $this->session->get('jeu')->getPlayers(),
			'status' => 'light',
			'niveau' => 'Le jeu est prêt ! Cliquez ici pour passer aux étapes suivantes',
			'score'  => 0,
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

			return $this->render('accueil.html.twig',[
				'players' => $this->session->get('jeu')->getPlayers(),
				'niveau'  => $level->find($niveau),
				'status'  => $level->find($niveau)->getStatus(),
				'score'   => $this->session->get('jeu')->getScore(),
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
				$this->saveBank(-$this->session->get('jeu')->getScore());

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

				return $this->render('accueil.html.twig',[
					'players'=> $this->session->get('jeu')->getPlayers(),
					'status' => 'warning',
					'niveau' => "Vous avez gagné ! ".$this->session->get('jeu')->getScore()." € ! Banco ?",
					'score' => $this->session->get('jeu')->getScore(),
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
	                'reponse' =>['Oui', 'Non'],                                     
	                'question'=>['question'=>['question'=>"Si vous gagnez, vous doublez vos gains ?",'answer'=>"Si vous perdez, vous perdez tout."]]                            
	            ]);
	        } else {
	        	return $this->redirectToRoute('pertes',[],301);
	        }

		}

		if ($niveau == 5){

			if ( $reponse == "good" ){

				$this->saveBank(-1000);

				$this->session->get('jeu')->addAllScores(1000);

				$em->flush();
				$this->session->set('contexte',"fin");		

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
	public function scores(): Response
	{
		$players = $this->p->findSorted();

		return $this->render('scores.html.twig',[
			'players'=> $players
		]);
	}
	/**
	 * @Route("/init_scores", name="init_scores")
	 * @return Response
	 *
	 */
	public function init_scores(): Response
	{
		$players = $this->p->findAll();
		$em = $this->getDoctrine()->getManager();

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
	public function gains($gains): Response
	{
		$this->session->set('contexte',"fin");

		$em = $this->getDoctrine()->getManager();

		$gains=$gains/2;

		$this->p->find($this->session->get('jeu')->getPlayers()[0]->getId())->addScore($gains);
		$this->p->find($this->session->get('jeu')->getPlayers()[1]->getId())->addScore($gains);
		
		$em->flush();

		return $this->render('accueil.html.twig',[
			'players' => $this->session->get('jeu')->getPlayers(),
			'niveau'  => "*** Vous avez gagné chacun ".$gains." € ! ***",
			'status'  => 'warning',
			'score'   => $this->session->get('jeu')->getScore(),
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

		$this->saveBank($perte);

		$this->init();

		$this->session->set('contexte',"fin");

		return $this->render('accueil.html.twig',[
			'players'=> $this->session->get('jeu')->getPlayers(),
			'status' => 'info',
			'niveau' => "* Vous avez perdu cette partie et tous vos gains sont allés à la banque ! *",
			'score' => 0,
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

		return $this->render('accueil.html.twig',[
			'status' => 'light',
			'niveau' => 'Cliquez ici pour commencer un nouveau jeu',
			'score' => 0,
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
	public function players(JeuRepository $j, Request $request): Response
	{
		$em = $this->getDoctrine()->getManager();
		$jeu = $this->session->get('jeu');
		$players = $this->session->get('players');
		$duo = new Duo();

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

	private function preparation(){

		$jeu = new Jeu();//une manche du jeu
		$steps = array();array_push($steps,0);//étapes du jeu
		$players = array();//joueurs

		$nb_bleues = $this->q->findAllByLevel(1);
		$nb_blanches = $this->q->findAllByLevel(2);
		$nb_rouges = $this->q->findAllByLevel(3);
		$nb_bancos = $this->q->findAllByLevel(4);
		$nb_supers = $this->q->findAllByLevel(5);
		$nb_players = $this->p->findAll();
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

			//on sélectionne 3 questions au hasard
			$rand_nb_bleues = array_rand($nb_bleues,3);

			for ($i = 1; $i < 4 ; $i++) {
				$step = new Step($nb_bleues[$rand_nb_bleues[ $i-1 ]]);
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
	}

	/**
	 * @Route("/bug", name="bug")
	 * @return Response
	 *
	 */
	public function bug(PlayerRepository $p): Response{
		$em = $this->getDoctrine()->getManager();

		return $this->render('bug.html.twig',[
			'players'=> $this->session->get('jeu')->getPlayers()
		]);
	}

	private function saveBank(int $gain){
		$request = new Request;

		$bank = $request->cookies->get('bank');
		$cookie = new Cookie('bank', $bank+$gain, time()+365*24*60*60);
		$res = new Response;
		$res->headers->setCookie($cookie);
		$res->send();
	}
}
?>
