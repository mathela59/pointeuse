<?php

namespace App\Controller;

use App\Entity\Log;
use App\Repository\LogRepository;
use Symfony\Component\DomCrawler\Field\ChoiceFormField;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class PointeuseController extends Controller
{
    /**
     * @Route("/", name="homepage")
     */
    public function index(Request $request)
    {
        return $this->render('pointeuse/index.html.twig', [
            'controller_name' => 'PointeuseController',
        ]);
    }

    /**
     * @Route("/garde/", name="garde")
     */
    public function addGardeEnfant(Request $request)
    {

        $today = date("Y-m-d");
        $fin = new \DateTime(date("Y-m-d H:i:s"));
        $debut = new \DateTime($today . ' ' . (date("w") == 1 ? '16:30:00' : '16:45:00'));

        $log = new Log();
        $log->setDebut($debut);
        $log->setFin($fin);
        $log->setJob("Garde");

        $form = $this->createFormBuilder($log)
            ->add("Debut", DateTimeType::class, array("attr" => array("readonly" => true), 'widget' => 'single_text', 'format' => 'yyyy-MM-dd HH:mm:ss'))
            ->add("Fin", DateTimeType::class, array("attr" => array("readonly" => true), 'widget' => 'single_text', 'format' => 'yyyy-MM-dd HH:mm:ss'))
            ->add("Job", TextType::class, array("attr" => array("readonly" => true)))
            ->add('Save', SubmitType::class)
            ->getForm();

        if ($form->handleRequest($request)->isValid()) {
            //do post treatments
            $em = $this->getDoctrine()->getManager();
            $params = $request->request->get('form');
            $log = new Log();
            $log->setDebut(new \DateTime($params["Debut"]));
            $log->setFin(new \DateTime($params['Fin']));
            $log->setJob($params["Job"]);
            $em->persist($log);
            $em->flush();

            //then
            return $this->redirectToRoute('homepage');
        }

        return $this->render('pointeuse/garde.html.twig', [
            'controller_name' => 'PointeuseController', "form" => $form->createView()
        ]);
    }

    /**
     * @Route("/docteur/", name="docteur")
     */
    public function addDocteur(Request $request)
    {
        $time = new \DateTime(date("Y-m-d H:i:s"));

        $today = date("Y-m-d");
        $fin = new \DateTime(date("Y-m-d H:i:s"));
        $debut = new \DateTime($today . ' ' . (date("w") == 1 ? '16:30:00' : '16:45:00'));

        $log = new Log();
        $log->setDebut($debut);
        $log->setFin($fin);
        $log->setJob("Garde");


        $lr = $this->getDoctrine()->getRepository(Log::class);
        $lastLog = $lr->findLastLog();
        $dateJalon = new \DateTime(date("Y-m-d H:i:s", strtotime("2099-01-01 00:00:00")));

        if (count($lastLog) > 0) $lastLog = $lastLog[0]; else $lastLog = null;

        /** @var DateTime $lastFin */
        $lastFin = $lastLog->getFin();

        if (isset($lastLog) && $lastFin->format("U") != $dateJalon->format("U")) {
            $log = new Log();
            $log->setDebut($time);
            $log->setFin(new \DateTime(date("Y-m-d H:i:s", strtotime("2099-01-01 00:00:00"))));
            $log->setJob("Docteur");

            $form = $this->createFormBuilder($log)
                ->add("Debut", DateTimeType::class, array("attr" => array("readonly" => true), 'widget' => 'single_text', 'format' => 'yyyy-MM-dd HH:mm:ss'))
                ->add("Fin", DateTimeType::class, array("attr" => array("readonly" => true), 'widget' => 'single_text', 'format' => 'yyyy-MM-dd HH:mm:ss'))
                ->add("Job", TextType::class, array("attr" => array("readonly" => true)))
                ->add('Save', SubmitType::class)
                ->getForm();

        } else {
            $lastLog->setFin($time);

            $form = $this->createFormBuilder($lastLog)
                ->add("Debut", DateTimeType::class, array("attr" => array("readonly" => true), 'widget' => 'single_text', 'format' => 'yyyy-MM-dd HH:mm:ss'))
                ->add("Fin", DateTimeType::class, array("attr" => array("readonly" => true), 'widget' => 'single_text', 'format' => 'yyyy-MM-dd HH:mm:ss'))
                ->add("Job", TextType::class, array("attr" => array("readonly" => true)))
                ->add('Save', SubmitType::class)
                ->getForm();
        }


        if ($form->handleRequest($request)->isValid()) {
            //do post treatments
            $em = $this->getDoctrine()->getManager();
            $params = $request->request->get('form');
            $log = new Log();
            if (array_key_exists("Debut", $params)) {
                $log->setDebut(new \DateTime($params["Debut"]));
            }
            if (array_key_exists("Fin", $params)) {
                $log->setFin(new \DateTime($params["Fin"]));
            }
            $log->setJob($params["Job"]);
            $em->persist($log);
            $em->flush();

            //then
            return $this->redirectToRoute('homepage');
        }


        return $this->render('pointeuse/docteur.html.twig', [
            'controller_name' => 'PointeuseController', 'form' => $form->createView()
        ]);
    }


    /**
     * @Route("/list/", name="liste")
     */
    public function liste(Request $request)
    {
        $firstDayOfMonthStr = new \DateTime(date("Y-m-01 00:00:00"));
        $lastDayOfMonth = new \DateTime(date("Y-m-t 23:59:59"));
        $form = $this->createFormBuilder()
            ->add("Debut", DateTimeType::class, array('widget' => 'single_text', 'format' => 'yyyy-MM-dd HH:mm:ss', 'input' => 'datetime', "data" => $firstDayOfMonthStr))
            ->add("Fin", DateTimeType::class, array('widget' => 'single_text', 'format' => 'yyyy-MM-dd HH:mm:ss', 'input' => 'datetime', 'data' => $lastDayOfMonth))
            ->add("Job", ChoiceType::class, array("choices" => ["Docteur" => "Docteur", "Garde" => "Garde"]))
            ->add('Chercher', SubmitType::class)
            ->getForm();


        if ($form->handleRequest($request)->isValid()) {
            //do post treatments

            $lr = $this->getDoctrine()->getRepository(Log::class);

            $params = $request->request->get('form');

            $liste = $lr->findByDatesAndJobs(new \DateTime($params["Debut"]), new \DateTime($params["Fin"]), $params['Job']);


            //calculate some duration
            $results = array();
            $totalDuration = 0;
            /** @var Log $item */
            foreach ($liste as $item) {
                $results[] = array(
                    "id" => $item->getId(),
                    "Debut" => $item->getDebut(),
                    "Fin" => $item->getFin(),
                    "Job" => $item->getJob(),
                    "Duration" => date_diff($item->getDebut(), $item->getFin())->format("%i"));
                $totalDuration+=date_diff($item->getDebut(), $item->getFin())->format("%i");
            }

            $totalHeures = $totalDuration/60;
            $totalMinutes = $totalDuration%60;

            //then
            return $this->render('pointeuse/listing.html.twig', [
                'controller_name' => 'PointeuseController', 'form' => $form->createView(), 'liste' => $results,"heures"=>(integer) $totalHeures, "minutes"=>$totalMinutes
            ]);
        }


        return $this->render('pointeuse/listing.html.twig', [
            'controller_name' => 'PointeuseController', 'form' => $form->createView()
        ]);
    }
}
