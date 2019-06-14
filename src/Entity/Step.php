<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\StepRepository")
 */
class Step
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="integer")
     */
    private $suite;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Question")
     */
    private $question;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getSuite(): ?int
    {
        return $this->suite;
    }

    public function setSuite(int $suite): self
    {
        $this->suite = $suite;

        return $this;
    }

    public function getQuestion(): ?Question
    {
        return $this->question;
    }

    public function setQuestion(?Question $question): self
    {
        $this->question = $question;

        return $this;
    }
}
