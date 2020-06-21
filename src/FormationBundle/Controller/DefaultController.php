<?php

namespace FormationBundle\Controller;

use FormationBundle\Entity\Bane;
use FormationBundle\Entity\Formation;
use FormationBundle\Entity\participation;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use FormationBundle\Repository\FormationRepository;

class DefaultController extends Controller
{
    public function indexAction(Request $request)
    {
        $user = $this->container->get('security.token_storage')->getToken()->getUser();
        $em = $this->getDoctrine()->getManager();
        $formations = $em->getRepository('FormationBundle:Formation')->findAll();

        $count = count($formations);

        if ($request->isMethod('POST')) {

            $c = array() ;

            foreach ($formations as $p)
            {
                if(strstr(strtolower($p->getNom()),$request->get('search')))  array_push($c,$p);
            }

            return $this->render('FormationBundle:Default:index.html.twig'
                ,array(
                    'formations' => $c,
                    'count' => count($c)
                ));
        }

        foreach ($formations as $f)
        {
            $bane = $em->getRepository('FormationBundle:Bane')->findBy([
                "idFormation"=>$f->getId(),
                "idUser"=>$user->getId()

            ]);
            if(count($bane)>0) {
                $f->setBane(true);
            }else {
                $f->setBane(false);
            }
        }
        return $this->render('FormationBundle:Default:index.html.twig'
            ,array(
                'formations' => $formations,
                'count' => $count
            ));
    }

    public function detailsAction($id,Request $request)
    {
        $user = $this->container->get('security.token_storage')->getToken()->getUser();
        $em = $this->getDoctrine()->getManager();
        $formation = $em->getRepository('FormationBundle:Formation')->find($id);

        $participe = $em->getRepository('FormationBundle:participation')->findBy([
            "idUser"=>$user->getId(),
            "idFormation"=>$id
        ]);

        $count = count($participe);

        return $this->render('FormationBundle:Default:DetailsFormations.html.twig'
            ,array(
                'formation' => $formation,
                 'count' => $count
            ));
    }

    public function reserverAction($id,Request $request)
    {
        $user = $this->container->get('security.token_storage')->getToken()->getUser();
        $em = $this->getDoctrine()->getManager();
        $formation = $em->getRepository('FormationBundle:Formation')->find($id);

        $participation = new participation();

            $participation->setIdFormation($formation);
            $participation->setIdUser($user);

            // add of reservation
            $em = $this->getDoctrine()->getManager();
            $em->persist($participation);
            $em->flush();

            $formation->setNombrePlaces($formation->getNombrePlaces()-1);
            // min quantité of the product
            $em = $this->getDoctrine()->getManager();
            $em->persist($formation);
            $em->flush();

            //sms sender
        // comment this
       $basic  = new \Nexmo\Client\Credentials\Basic('4b4acd7c', '6Nk97zG3kM6k8Xtn');
       $client = new \Nexmo\Client($basic);
       $message = $client->message()->send([
           'to' => '21692110495',
           'from' => 'Hunt-App',
           'text' => 'Votre participation a été enregistré avec succées'
       ]);
       //comment this

            $request->getSession()
                ->getFlashBag()
                ->add('success', 'vous étes inscri à la formation ...!');

            $url = $this->generateUrl('details_formations', ['id' => $id]);
            return $this->redirect($url);

    }

    public function annulerAction($id,Request $request)
    {
        $user = $this->container->get('security.token_storage')->getToken()->getUser();
        $em = $this->getDoctrine()->getManager();
        $formation = $em->getRepository('FormationBundle:Formation')->find($id);



        $participation = $em->getRepository('FormationBundle:participation')->findOneBy([
            "idUser"=>$user->getId(),
            "idFormation"=>$id
        ]);


        $em =$this->getDoctrine()->getManager();
        $em->remove($participation);
        $em->flush();

        $formation->setNombrePlaces($formation->getNombrePlaces()+1);
        // min quantité of the product
        $em = $this->getDoctrine()->getManager();
        $em->persist($formation);
        $em->flush();

        $request->getSession()
            ->getFlashBag()
            ->add('success', 'vous avez annuler votre inscription à la formation ...!');

        $url = $this->generateUrl('details_formations', ['id' => $id]);
        return $this->redirect($url);

    }

    public function voirparticipantsAction($id,Request $request)
    {
        $user = $this->container->get('security.token_storage')->getToken()->getUser();
        $em = $this->getDoctrine()->getManager();
        $formation = $em->getRepository('FormationBundle:Formation')->find($id);

        $participe = $em->getRepository('FormationBundle:participation')->findBy([
            "idFormation"=>$id
        ]);

        foreach ($participe as $p)
        {
            $bane = $em->getRepository('FormationBundle:Bane')->findBy([
                "idFormation"=>$p->getIdFormation(),
                "idUser"=>$p->getIdUser()->getId()
            ]);

            if(count($bane)>0) {
                $p->setBane(true);
            }else {
                $p->setBane(false);
            }
        }

        $count = count($participe);
        //var_dump($count);
       //change this to the new interfaces
       return $this->render('FormationBundle:Default:listParticipants.html.twig'
           ,array(
               'participants' => $participe,
               'count' => $count,
               'user'=>$user
           ));
    }


    public function backFormationsAction()
    {
        $user = $this->container->get('security.token_storage')->getToken()->getUser();
        $em = $this->getDoctrine()->getManager();
        $formations = $em->getRepository('FormationBundle:Formation')->findAll();

        $count = count($formations);

        return $this->render('FormationBundle:Default:listFormations.html.twig'
            ,array(
                'user'=>$user,
                'formations' => $formations,
                'count' => $count
            ));
    }
    public function ajoutFormationsAction(Request $request)
    {
        $user = $this->container->get('security.token_storage')->getToken()->getUser();
        $em = $this->getDoctrine()->getManager();

        if ($request->isMethod('POST')) {

            $for = new Formation();
            $for->setNom($request->get('nom'));
            $for->setType($request->get('type'));
            $for->setLieu($request->get('lieu'));
            $for->setDateFin(new \DateTime($request->get('datefin')));
            $for->setDateDebut(new \DateTime($request->get('dateDebut')));
            $for->setFormateur($request->get('formateur'));
            $for->setNombrePlaces($request->get('nbr'));

            //validation

            $error = false ;
            $error_str='';

            if( strlen($for->getNom()) == 0) {
                $error=true;
                $error_str='Veuillez remplir tous les champs';
            }

            if( strlen($for->getFormateur()) == 0) {
                $error=true;
                $error_str='Veuillez remplir tous les champs';
            }

            if( strlen($for->getLieu()) == 0) {
                $error=true;
                $error_str='Veuillez remplir tous les champs';
            }

            if( strlen($for->getType()) == 0) {
                $error=true;
                $error_str='Veuillez remplir tous les champs';
            }
            if( strlen($for->getNombrePlaces()) == 0) {
                $error=true;
                $error_str='Veuillez remplir tous les champs';
            }

            if($for->getDateDebut()> $for->getDateFin()) {
                $error=true;
                $error_str='Date debut > date fin';
            }

            if($for->getDateDebut()< new \DateTime("now")) {
                $error=true;
                $error_str='Date Debut invalide';
            }

            if($for->getDateFin()< new \DateTime("now")) {
                $error=true;
                $error_str='Date fin invalide';
            }

            if($error==true) {

                $request->getSession()
                    ->getFlashBag()
                    ->add('error', $error_str);

                $url = $this->generateUrl('back_ajout');
                return $this->redirect($url);
            }

            $em = $this->getDoctrine()->getManager();
            $em->persist($for);
            $em->flush();


            $request->getSession()
                ->getFlashBag()
                ->add('success', 'Formation ajouté avec succées ...!');


            $url = $this->generateUrl('back_formations');

            return $this->redirect($url);

        }

        return $this->render('FormationBundle:Default:ajoutBack.html.twig'
            ,array(
                'user'=>$user,
            ));
    }

    public function modifFormationsAction($id,Request $request)
    {
        $user = $this->container->get('security.token_storage')->getToken()->getUser();
        $em = $this->getDoctrine()->getManager();

        $for = $em->getRepository('FormationBundle:Formation')->find($id);

        if ($request->isMethod('POST')) {

            $for->setNom($request->get('nom'));
            $for->setType($request->get('type'));
            $for->setLieu($request->get('lieu'));
            $for->setDateFin(new \DateTime($request->get('datefin')));
            $for->setDateDebut(new \DateTime($request->get('dateDebut')));
            $for->setFormateur($request->get('formateur'));
            $for->setNombrePlaces($request->get('nbr'));

            //validation

            $error = false ;
            $error_str='';

            if( strlen($for->getNom()) == 0) {
                $error=true;
                $error_str='Veuillez remplir tous les champs';
            }

            if( strlen($for->getFormateur()) == 0) {
                $error=true;
                $error_str='Veuillez remplir tous les champs';
            }

            if( strlen($for->getLieu()) == 0) {
                $error=true;
                $error_str='Veuillez remplir tous les champs';
            }

            if( strlen($for->getType()) == 0) {
                $error=true;
                $error_str='Veuillez remplir tous les champs';
            }
            if( strlen($for->getNombrePlaces()) == 0) {
                $error=true;
                $error_str='Veuillez remplir tous les champs';
            }

            if($for->getDateDebut()> $for->getDateFin()) {
                $error=true;
                $error_str='Date debut > date fin';
            }

            if($for->getDateDebut()< new \DateTime("now")) {
                $error=true;
                $error_str='Date Debut invalide';
            }

            if($for->getDateFin()< new \DateTime("now")) {
                $error=true;
                $error_str='Date fin invalide';
            }
            if($error==true) {

                $request->getSession()
                    ->getFlashBag()
                    ->add('error', $error_str);

                $url = $this->generateUrl('back_modifier' , ['id' => $id]);
                return $this->redirect($url);
            }

            $em = $this->getDoctrine()->getManager();
            $em->persist($for);
            $em->flush();


            $request->getSession()
                ->getFlashBag()
                ->add('success', 'Formation Modifier avec succées ...!');


            $url = $this->generateUrl('back_formations');

            return $this->redirect($url);

        }

        return $this->render('FormationBundle:Default:modifBack.html.twig'
            ,array(
                'user'=>$user,
                'for'=>$for
            ));
    }

    public function supprimerAction($id,Request $request)
    {

        $user = $this->container->get('security.token_storage')->getToken()->getUser();


        $participation = $this->getDoctrine()->getRepository('FormationBundle:participation')->find($id);
        // var_dump($forum);

        $em =$this->getDoctrine()->getManager();
        $em->remove($participation);
        $em->flush();
        $request->getSession()
            ->getFlashBag()
            ->add('success', 'Participation a été supprimer avec succées ...!');

        $url = $this->generateUrl('voir_participants', ['id' => $participation->getIdFormation()->getId()]);
        return $this->redirect($url);
    }

    public function baneAction($id,Request $request)
    {

        $user = $this->container->get('security.token_storage')->getToken()->getUser();


        $participation = $this->getDoctrine()->getRepository('FormationBundle:participation')->find($id);

        $bane = new Bane();
        $bane->setIdFormation($participation->getIdFormation()->getId());
        $bane->setIdUser($participation->getIdUser()->getId());
        $bane->setBane(true);


        $em =$this->getDoctrine()->getManager();
        $em->persist($bane);
        $em->flush();

        $request->getSession()
            ->getFlashBag()
            ->add('success', 'ce utilisateur ne peux plus voir le contenu ...!');

        $url = $this->generateUrl('voir_participants', ['id' => $participation->getIdFormation()->getId()]);
        return $this->redirect($url);
    }
}
