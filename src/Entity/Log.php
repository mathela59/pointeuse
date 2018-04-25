<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\LogRepository")
 */
class Log
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="datetime")
     */
    private $debut;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $fin;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $job;

    public function getId()
    {
        return $this->id;
    }

    public function getDebut()
    {
        return $this->debut;
    }

    public function setDebut(\DateTime $debut): self
    {
        $this->debut = $debut;

        return $this;
    }

    public function getFin()
    {
        return $this->fin;
    }

    public function setFin(\DateTime $fin): self
    {
        $this->fin = $fin;

        return $this;
    }

    public function getJob()
    {
        return $this->job;
    }

    public function setJob(string $job): self
    {
        $this->job = $job;

        return $this;
    }
}
