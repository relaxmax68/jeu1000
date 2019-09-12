<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\Question;
use App\Form\QuestionType;
use App\Repository\QuestionRepository;
use Symfony\Component\HttpFoundation\Request;

/**
  * @Route("/questions")
  */ 
class QuestionController extends AbstractController
{
	 /**
     * @Route("/", name="question_liste")
     */

    //Edition des questions 
    public function index(QuestionRepository $qrepo)
    {
 		$questions = $qrepo->listeQuestionsClassees();
        return $this->render('question/index.html.twig', [
            'questions' => $questions   
        ]);
    }

    /**
     * @Route("/ajouter", name="question_ajouter", methods={"GET", "POST"})
     */

    //Ajouter une nouvelle question 
    public function ajouter(Request $request)
    {
        $question = new Question();
        $form = $this->createForm(QuestionType::class, $question);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->persist($question);
            $this->getDoctrine()->getManager()->flush();
            return $this->redirectToRoute('question_liste');
        }
        return $this->render('question/ajouter.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="question_edit" , methods={"GET", "POST"})
     */

    //Edition de la question n°id 
    public function edit(Question $question ,Request $request)
    {
    	$form = $this->createForm(QuestionType::class, $question);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();
            return $this->redirectToRoute('question_liste');
        }
        return $this->render('question/edit.html.twig', [
            'form' => $form->createView(),
            'id' => $question->getId()
        ]);
    }

    /**
    * @Route("/{id}", name="question_supprimer", methods={"DELETE"})
    */
    public function supprimer(Question $question ,Request $request)
    {
        if ($this->isCsrfTokenValid("delete".$question->getId(), $request->request->get("_token"))) {
            $this->getDoctrine()->getManager()->remove($question);
            $this->getDoctrine()->getManager()->flush();
            return $this->redirectToRoute('question_liste');
        }

    }


}
