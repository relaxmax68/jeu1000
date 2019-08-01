<?php

namespace App\Twig;

use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;
use Twig\TwigFunction;

use App\Repository\QuestionRepository;

class compteurQuestions extends AbstractExtension
{
    private $repo;

    public function __construct(QuestionRepository $repo)
    {
    	$this->repo = $repo;
    }


    public function getFunctions()
    {
        return [
            new TwigFunction('compteur', [$this, 'functionCompteur']),
        ];
    }

    public function functionCompteur()
    {
        return count($this->repo->findAll());
    }
}
