<?php
namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Twig\Environment;

use App\Entity\Step;
use App\Entity\Question;

use App\Repository\QuestionRepository;
use App\Repository\LevelRepository;

use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\HttpFoundation\Cookie;


class GameController extends AbstractController
{
	private $session;

	public function __construct(SessionInterface $session,QuestionRepository $q)
	{
		$this->session = $session;
		$this->q = $q;
	}

	/**
	 * @Route("/", name="home")
	 * @return Response
	 *
	 */
	public function home(Request $request): Response
	{
		$this->init();

		$this->session->set('contexte',"pause");//contexte de jeu : pause / questions / banco
		
		$list_players = unserialize($request->cookies->get('jeu1000'));
		$this->session->set('list_players', $list_players);

		// sélection des joueurs
		if( count($list_players) < 2 ){
			$this->addFlash('warning','Il faut au minimum deux joueurs');
			return $this->redirectToRoute('scores',[],301);	
		} else {
			$this->preparation();
		}

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
			return $this->redirectToRoute('home',[],301);
		}else{
			
			$step = $this->session->get('step');
					dump($this->session->get('juste'));

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
				return $this->redirectToRoute('scores',[],301);			
			}
		}

		if ( $contexte == "jeu" ){
			// on retire la question posée de la pile
			$step = array_shift($steps);

			//bonnes réponses
			if ($reponse == "good") {
				$this->session->set('score', $this->session->get('score') + $gain);
				$this->session->set('juste',$juste+1);
			}
			// mauvaises réponses
			if ($reponse == "bad") {
				if ($this->session->get('chance') == 0) {
					array_push($steps,$step); //on remet la question dans la pile pour une 2ème chance
				}
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
				return $this->redirectToRoute('home',[],301);
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
			if ($this->session->get('juste') == 6 ){

				$this->session->set('contexte',"choix");
				$this->session->set('chance',0);//à partir de maintenant il n'est plus question de deuxième chance

				return $this->render('accueil.html.twig',[
					'status' => 'warning',
					'niveau' => "Vous avez gagné ! ".$this->session->get('score')." € ! Banco ?",
					'reponse' =>['Oui', 'Non'],					
				]);				
			}else{
	        	return $this->redirectToRoute('pertes',[],301);
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
		//on classe les joueurs d'après leur score
		$list_players = $this->session->get('list_players');
		arsort($list_players);

		return $this->render('scores.html.twig',[
			'players' => $list_players,
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

	    return $this->redirectToRoute('home',[],301);
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

	private function init(){
		$this->session->set('niveau',0);
		$this->session->set('juste',0);
		$this->session->set('step', 0);
		$this->session->set('steps', array());
		$this->session->set('question',0);
		$this->session->set('chance',0);
		$this->session->set('score',0);
		$this->session->set('contexte',"pause");
	}

	private function preparation(){

		$steps = array();//étapes du jeu
		$players = array();//joueurs

		$list_players = $this->session->get('list_players');
		//on mélange le tableau
		$this->shuffle_assoc($list_players);			
		$players = $this->array_pshift($list_players);

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

		$this->session->set('list_players', $list_players);
		
		$this->setCookie();
	}

	/**
	 * @Route("/bug", name="bug")
	 * @return Response
	 *
	 */
	public function bug(): Response{

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
	private function setCookie() {

		$list_players= $this->session->get('list_players');

		$res = new Response;
		$cookie = new Cookie('jeu1000',serialize($list_players), time()+365*24*60*60);
		$res->headers->setCookie($cookie);
		$res->send();
	}
	/**
	 * @Route("/add", name="addplayer")
	 * @return 
	 */
	public function add(){

		if(!isset($_POST['name']) || empty($_POST['name'])){
			header('500 Internal Server Error', true, 500);
			$this->addFlash('warning','Vous devez renseigner le nom du joueur');
			return $this->redirectToRoute('scores',[],301);	
		}
		$list_players= $this->session->get('list_players');

		//traitement
		$list_players[$_POST['name']] = 0;

		$this->session->set('list_players', $list_players);		
		$this->setCookie();

		return $this->redirectToRoute('scores',[],301);	

	}
	/**
	 * @Route("/remove/{id}", name="removeplayer")
	 * @return 
	 */
	public function remove($id){

		$list_players= $this->session->get('list_players');

		//traitement
		unset($list_players[$id]);

		$this->session->set('list_players', $list_players);
		$this->setCookie();

		return $this->redirectToRoute('scores',[],301);
	}	
}
?>
