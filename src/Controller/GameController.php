<?php
namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Twig\Environment;

use App\Entity\Step;
use App\Entity\Player;
use App\Entity\Question;
use App\Entity\Duo;

use App\Repository\QuestionRepository;
use App\Repository\LevelRepository;
use App\Repository\PlayerRepository;
use App\Repository\StepRepository;
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
	 * @Route("/", name="home")
	 * @return Response
	 *
	 */
	public function new(Request $request): Response
	{
		$this->init();

		$this->session->set('contexte',"pause");//contexte de jeu : pause / questions / banco
		
		$this->preparation($request);

		return $this->render('accueil.html.twig',[
			'status' => 'light',
			'reponse' =>['Commencer Le jeu', 'Ajouter un joueur'],			
			'niveau' => 'Le jeu est prêt !',
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
			
			$step = $this->session->get('step');

			$question = $this->session->get('steps')[$step];
			$niveau = $question->getQuestion()->getLevel()->getId();
			$this->session->set('niveau',$niveau);
			$this->session->set('gain',$level->find($niveau)->getScore());

			return $this->render('accueil.html.twig',[
				'niveau'  => $level->find($niveau),
				'status'  => $level->find($niveau)->getStatus(),
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
			if ($reponse == "good"){
				$contexte = $this->session->set('contexte',"jeu");
				return $this->redirectToRoute('question',[],301);
			}
			if ($reponse == "bad"){
				return $this->redirectToRoute('players',301);			
			}
		}

		if ( $contexte == "jeu" ){
			// on retire la question
			$step = array_shift($steps);

			//bonnes réponses
			if ($reponse == "good") {
				$this->session->set('score', $this->session->get('score') + $gain);
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
				$this->session->set('bank', $this->session->get('bank')-$this->session->get('score'));

				return $this->redirectToRoute('gains',['gains'=> $this->session->get('score')],301);
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
		$niveau = $this->session->get('niveau');
		$steps = $this->session->get('steps');

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
					'status' => 'warning',
					'niveau' => "Vous avez gagné ! ".$this->session->get('score')." € ! Banco ?",
					'reponse' =>['Oui', 'Non'],					
				]);
			}
		}

		if ($niveau == 4) {

			if ( $reponse == "good" ){

				$this->session->set('contexte',"choix");
				$this->session->set('score',500);				

				return $this->render('accueil.html.twig',[
	                'status' => 'warning',
	                'niveau' => "Vous avez gagné 500€ ! Super Banco ?",
	                'reponse' =>['Oui', 'Non'],                     
	            ]);
	        } else {
	        	return $this->redirectToRoute('pertes',[],301);
	        }

		}

		if ($niveau == 5){

			if ( $reponse == "good" ){

				$this->session->set('bank', $this->session->get('bank')-1000);

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
	public function scores(Request $request): Response
	{
		$list_players = $this->session->get('list_players');

		return $this->render('scores.html.twig',[
			'players'=> $list_players
		]);
	}
	/**
	 * @Route("/init_scores", name="init_scores")
	 * @return Response
	 *
	 */
	public function init_scores(): Response
	{
		$list_players = $this->session->get('list_players');
		foreach ($list_players as $key => $value){
			$list_players[$key] = 0;
		}

		$this->session->set('list_players', $list_players);

		$res = new Response;
		$cookie = new Cookie('jeu1000',serialize($this->session->get('list_players')), time()+365*24*60*60);
		$res->headers->setCookie($cookie);
		$res->send();		

		return $this->redirectToRoute('scores',[],301);
	}
	/**
	 * @Route("/init", name="init")
	 * @return Response
	 *
	 */
	public function initGame(): Response
	{
		session_unset();

		$this->session->set('bank', 0);
		$this->session->set('score', 0);

		return $this->render('accueil.html.twig',[
			'status' => 'light',
			'niveau' => 'Cliquez ici pour commencer un nouveau jeu',
		]);
	}

	/**
	 * @Route("/gains/{gains}", name="gains")
	 * @return Response
	 *
	 */
	public function gains($gains): Response
	{
		$this->session->set('contexte',"fin");

		$gains = $gains/2;

		$this->saveScores($gains);
		
		return $this->render('accueil.html.twig',[
			'niveau'  => "*** Vous avez gagné chacun ".$gains." € ! ***",
			'status'  => 'warning',
			'reponse' =>['Nouveau Jeu', 'Scores'],
		]);
	}
	/**
	 * @Route("/pertes", name="pertes")
	 * @return Response
	 *
	 */
	public function pertes (){

		//la banque prend ses gains
		$this->session->set('bank', $this->session->get('bank') + $this->session->get('score'));

		$this->init();

		$this->session->set('contexte',"fin");
		$this->session->set('score',0);

		return $this->render('accueil.html.twig',[
			'status' => 'info',
			'niveau' => "* Vous avez perdu cette partie et tous vos gains sont allés à la banque ! *",
			'reponse' =>['Nouveau Jeu', 'Arrêter'],				
		]);
	}
	/**
	 * @Route("/players", name="players")
	 * @return Response
	 * pour modifier les joueurs sélectionnés
	 */
	public function players(Request $request): Response
	{
		$em = $this->getDoctrine()->getManager();
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
			$this->session->set('contexte', "jeu");

			return $this->redirectToRoute('jeu');
		}

		return $this->render('players.html.twig', [
			'players'=> $this->session->get('players'),			
			'form' => $form->createView()
		]);
	}

	private function init(){
		$this->session->set('niveau',0);
		$this->session->set('step', 0);
		$this->session->set('steps', array());
		$this->session->set('question',0);
		$this->session->set('chance',0);
		$this->session->set('score',0);
		$this->session->set('contexte',"pause");
	}

	private function preparation($request){

		$steps = array();//étapes du jeu
		$players = array();//joueurs

		$list_players = unserialize($request->cookies->get('jeu1000'));

		$this->session->set('list_players', $list_players);

		$this->shuffle_assoc($list_players);

		// sélection des joueurs
		if( count($list_players)<2 ){
			// créer les joueurs
			//array_push($players,0);
		} else {
			$players = $this->array_pshift($list_players);
		}

		$this->session->set('players', $players);		

		$nb_bleues = $this->q->findAllByLevel(1);
		$nb_blanches = $this->q->findAllByLevel(2);
		$nb_rouges = $this->q->findAllByLevel(3);
		$nb_bancos = $this->q->findAllByLevel(4);
		$nb_supers = $this->q->findAllByLevel(5);

		//sélection des questions bleues
		if(!empty($nb_bleues)){

			//on sélectionne 3 questions au hasard
			$rand_nb_bleues = array_rand($nb_bleues,3);

			for ($i = 1; $i < 4 ; $i++) {
				$step = new Step($nb_bleues[$rand_nb_bleues[ $i-1 ]]);
				array_push($steps,$step);
			}
		}
		//sélection des questions blanches
		if(!empty($nb_blanches)){		
			for ($i = 4; $i < 6 ; $i++) {
				$step = new Step($nb_blanches[random_int(0, count($nb_blanches)-1)]);
				array_push($steps,$step);
			}
		}
		//sélection de la question rouge
		if(!empty($nb_rouges)){
			$step = new Step($nb_rouges[random_int(0, count($nb_rouges)-1)]);
			array_push($steps,$step);
		}

		//sélection de la question banco
		if(!empty($nb_bancos)){
			$step = new Step($nb_bancos[random_int(0, count($nb_bancos)-1)]);
			array_push($steps,$step);			
		}
		//sélection de la question super banco
		if(!empty($nb_supers)){
			$step = new Step($nb_supers[random_int(0, count($nb_supers)-1)]);
			array_push($steps,$step);			
		}

		$this->session->set('steps', $steps);
	}

	private function saveScores(int $gain){
		$list_players = $this->session->get('list_players');
		$players = $this->session->get('players');

		foreach ($players as $p) {
			$list_players[$p] += $gain;
		}

		$res = new Response;
		$cookie = new Cookie('jeu1000',serialize($this->session->get('list_players')), time()+365*24*60*60);
		$res->headers->setCookie($cookie);
		$res->send();

		$this->session->set('list_players', $list_players);
	}

	/**
	 * @Route("/bug", name="bug")
	 * @return Response
	 *
	 */
	public function bug(PlayerRepository $p): Response{
		$em = $this->getDoctrine()->getManager();

		return $this->render('bug.html.twig',[
			'players'=> $this->session->get('players')
		]);
	}
	/**
	 * fonctions pour les tableaux
	 */
    private function shuffle_assoc(&$array) {
        $keys = array_keys($array);

        shuffle($keys);

        foreach($keys as $key) {
            $new[$key] = $array[$key];
        }

        $array = $new;

        return true;
    }
    private function array_pshift(&$array) {

    	$keys = array_keys($array);

    	$key = array_shift($keys);
    	$key1 = array_shift($keys);

    	return array(0 => $key, 1 =>$key1);
	}
}
?>
