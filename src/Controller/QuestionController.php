<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\Question;
use App\Form\QuestionType;
use App\Repository\QuestionRepository;

class QuestionController extends AbstractController
{
	 /**
     * @Route("/questions/", name="question_liste")
     */

    //Edition de la question n°id 
    public function index(QuestionRepository $qrepo)
    {
 		$questions = $qrepo->listeQuestionsClassees();
        return $this->render('question/index.html.twig', [
            'questions' => $questions   
        ]);
    }



    /**
     * @Route("/questions/{id}", name="question_edit")
     */

    //Edition de la question n°id 
    public function edit(Question $question)
    {
    	$form = $this->createForm(QuestionType::class, $question);
        return $this->render('question/edit.html.twig', [
            'form' => $form->createView()
        ]);
    }
}
