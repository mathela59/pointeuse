<?php

namespace App\Controller;

use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class PointeuseController extends Controller
{
    /**
     * @Route("/pointeuse", name="pointeuse")
     */
    public function index()
    {
        return $this->render('pointeuse/index.html.twig', [
            'controller_name' => 'PointeuseController',
        ]);
    }
}
