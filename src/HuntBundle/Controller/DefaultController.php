<?php

    namespace HuntBundle\Controller;

    use HuntBundle\Entity\Reclamation;
    use HuntBundle\Entity\Reservation;
    use Symfony\Bundle\FrameworkBundle\Controller\Controller;
    use Symfony\Component\HttpFoundation\Request;
    use Symfony\Component\Routing\Annotation\Route;

    class DefaultController extends Controller
    {



        public function indexAction(Request $request)
        {
            $user = $this->container->get('security.token_storage')->getToken()->getUser();
            $em = $this->getDoctrine()->getManager();
            $produits = $em->getRepository('HuntBundle:Produit')->findAll();
            //search
            $nb = 0;
            foreach ($produits as $p)
            {
                $nb = 0;
                $reservations = $em->getRepository('HuntBundle:Reservation')->findBy([
                        "idProduit"=>$p->getId(),
                ]);
                foreach ($reservations as $r)
                {
                    if(( $r->getDateFin() > new \DateTime("now")) && ($r->getDateDebut() < new \DateTime("now"))) $nb+=$r->getQuantite();
                }
                $p->setNbr($nb);
            }

            if ($request->isMethod('POST')) {
                $c = array() ;
                foreach ($produits as $p)
                {
                    if(strstr(strtolower($p->getTitre()),$request->get('search')))  array_push($c,$p);
                }
                return $this->render('HuntBundle:Default:index.html.twig'
                        ,array(
                                'produits' => $c,
                            // 'count' => $count
                        ));
            }

            return $this->render('HuntBundle:Default:index.html.twig'
                    ,array(
                            'produits' => $produits,
                        //'user' => $user
                    ));
        }

        public function produitreclamationsAction()
        {
            $user = $this->container->get('security.token_storage')->getToken()->getUser();
            $em = $this->getDoctrine()->getManager();
            $produits = $em->getRepository('HuntBundle:Produit')->findAll();
            return $this->render('HuntBundle:Default:produits_reclamations.html.twig'
                    ,array(
                            'produits' => $produits,
                        //'user' => $user
                    ));
        }

        public function reclamerAction($id,Request $request)
        {
            $user = $this->container->get('security.token_storage')->getToken()->getUser();
            $em = $this->getDoctrine()->getManager();
            $produit = $em->getRepository('HuntBundle:Produit')->find($id);



            $reclamation = new Reclamation();
            if ($request->isMethod('POST')) {

                $error = false ;
                $error_str='';


                $reclamation->setReclamation($request->get('reclamation'));
                $reclamation->setDate(new \DateTime("now"));
                $reclamation->setIdUser($user->getId());
                $reclamation->setSujet($request->get('sujet'));



                if( strlen($reclamation->getSujet()) == 0) {
                    $error=true;
                    $error_str='Veuillez remplir toutes les champs';
                }


                if   ( strlen($reclamation->getReclamation()) == 0) {
                    $error=true;
                    $error_str='Veuillez remplir toutes les champs';
                }



                if($error==true) {

                    $request->getSession()
                            ->getFlashBag()
                            ->add('error', $error_str);

                    $url = $this->generateUrl('reclamer', ['id' => $id]);
                    return $this->redirect($url);
                }

                $em = $this->getDoctrine()->getManager();
                $em->persist($reclamation);
                $em->flush();
                $request->getSession()
                        ->getFlashBag()
                        ->add('success', 'Reclamation ajouté avec succées ...!');


                $url = $this->generateUrl('hunt_homepage');
                return $this->redirect($url);

            }
            return $this->render('HuntBundle:Default:reclamer.html.twig', array(
                    'produit' => $produit
            ));
        }


        public function mesreclamationsAction()
        {
            $user = $this->container->get('security.token_storage')->getToken()->getUser();
            $em = $this->getDoctrine()->getManager();
            $reclamations = $em->getRepository('HuntBundle:Reclamation')->findBy([
                    "idUser"=>$user->getId()
            ]);
            return $this->render('HuntBundle:Default:mesReclamations.html.twig'
                    ,array(
                            'reclamations' => $reclamations,
                        //'user' => $user
                    ));
        }

        public function mesreservationsAction()
        {
            $user = $this->container->get('security.token_storage')->getToken()->getUser();
            $em = $this->getDoctrine()->getManager();
            $reclamations = $em->getRepository('HuntBundle:Reclamation')->findBy([
                    "idUser"=>$user->getId()
            ]);
            return $this->render('HuntBundle:Default:mesReclamations.html.twig'
                    ,array(
                            'reclamations' => $reclamations,
                        //'user' => $user
                    ));
        }


        public function supprimerAction($id,Request $request)
        {

            $user = $this->container->get('security.token_storage')->getToken()->getUser();


            $forum = $this->getDoctrine()->getRepository('HuntBundle:Reclamation')->find($id);
            // var_dump($forum);

            $em =$this->getDoctrine()->getManager();
            $em->remove($forum);
            $em->flush();
            $request->getSession()
                    ->getFlashBag()
                    ->add('success', 'Reclamation a été supprimer avec succées ...!');

            return $this->redirectToRoute('mes_reclamations');
        }


        public function modifRecAction($id,Request $request)
        {
            $user = $this->container->get('security.token_storage')->getToken()->getUser();
            $em = $this->getDoctrine()->getManager();
            $rec = $em->getRepository('HuntBundle:Reclamation')->find($id);
            // $pro = $em->getRepository('HuntBundle:Produit')->find($rec->);
            // $reclamation = new Reclamation();
            if ($request->isMethod('POST')) {

                $rec->setReclamation($request->get('reclamation'));
                $rec->setDate(new \DateTime("now"));
                $rec->setIdUser($user->getId());
                $rec->setSujet($request->get('sujet'));

                $em = $this->getDoctrine()->getManager();
                $em->persist($rec);
                $em->flush();


                $request->getSession()
                        ->getFlashBag()
                        ->add('success', 'Reclamation Modifier avec succées ...!');


                $url = $this->generateUrl('mes_reclamations');

                return $this->redirect($url);

            }


            return $this->render('HuntBundle:Default:modifRec.html.twig', array(
                    'rec' => $rec
            ));
        }

        public function reserverAction($id,Request $request)
        {

            $user = $this->container->get('security.token_storage')->getToken()->getUser();
            $em = $this->getDoctrine()->getManager();
            $produit = $em->getRepository('HuntBundle:Produit')->find($id);

            $nb = 0;

            $reservations = $em->getRepository('HuntBundle:Reservation')->findBy([

                    "idProduit"=>$id,

            ]);

            foreach ($reservations as $r)

            {

                if(( $r->getDateFin() > new \DateTime("now")) && ($r->getDateDebut() < new \DateTime("now"))) $nb+=$r->getQuantite();
            }

            $produit->setNbr($nb);

            $date= date_format(new \DateTime("now"),"d/m/Y");;
            // $pro = $em->getRepository('HuntBundle:Produit')->find($rec->);
            $reservation = new Reservation();

            if ($request->isMethod('POST')) {
                $nombre=0;

                $reservations = $em->getRepository('HuntBundle:Reservation')->findBy([
                        "idProduit"=>$id,
                ]);
                foreach ($reservations as $r)
                {
                    if(( $r->getDateFin() > $request->get('date_fin')) && ($r->getDateDebut() < $request->get('date_debut'))) {
                        $nombre+=$r->getQuantite();
                    } elseif (( $r->getDateFin() > $request->get('date_fin')) && ($r->getDateDebut() > $request->get('date_debut'))) {
                        $nombre+=$r->getQuantite();
                    }
                }
                // LEHNE YSIR TEST
                //  var_dump($nombre);
                if($nombre==0 && $request->get('quantite')>$produit->getQuantite()) {
                    $request->getSession()
                            ->getFlashBag()
                            ->add('error', 'Reservation erroné quantité ' .$request->get('quantite'). 'nest pas disponible entre cette date ...!');

                    $url = $this->generateUrl('reserver_produit', ['id' => $id]);
                    return $this->redirect($url);

                }

                elseif ($nombre!=0 && $request->get('quantite')>$nombre) {

                    $request->getSession()
                            ->getFlashBag()
                            ->add('error', 'Reservation erroné quantité ' .$request->get('quantite'). 'nest pas disponible entre cette date ...!');

                    $url = $this->generateUrl('reserver_produit', ['id' => $id]);
                    return $this->redirect($url);

                }

                $reservation->setDateDebut(new \DateTime($request->get('date_debut')));
                $reservation->setDateFin(new \DateTime($request->get('date_fin')));
                $reservation->setIdProduit($id);
                $reservation->setIdUser($user->getId());
                $reservation->setQuantite($request->get('quantite'));



                //validation

                $error = false ;
                $error_str='';

                if($reservation->getDateDebut()> $reservation->getDateFin()) {
                    $error=true;
                    $error_str='Date debut > date fin';
                }

                if($reservation->getDateDebut()< new \DateTime("now")) {
                    $error=true;
                    $error_str='Date Debut invalide';
                }

                if($reservation->getDateFin()< new \DateTime("now")) {
                    $error=true;
                    $error_str='Date fin invalide';
                }
                if($error==true) {

                    $request->getSession()
                            ->getFlashBag()
                            ->add('error', $error_str);

                    $url = $this->generateUrl('reserver_produit' , ['id' => $id]);
                    return $this->redirect($url);
                }

                // add of reservation
                $em = $this->getDoctrine()->getManager();
                $em->persist($reservation);
                $em->flush();


                $request->getSession()
                        ->getFlashBag()
                        ->add('success', 'Reservation enregistrer avec succées ...!');

                $url = $this->generateUrl('hunt_homepage');
                return $this->redirect($url);

            }

            return $this->render('HuntBundle:Default:DetailsProduit.html.twig', array(
                    'p' => $produit,
                    'user'=>$user,
                    'date'=>$date
            ));
        }

        public function indexAdminAction(Request $request)
        {
            $user = $this->container->get('security.token_storage')->getToken()->getUser();
            $em = $this->getDoctrine()->getManager();
            $produits = $em->getRepository('HuntBundle:Produit')->findAll();


            return $this->render('HuntBundle:Backend:index.html.twig', array(
                    'user'=>$user,
            ));
        }

        public function recAdminAction()
        {
            $user = $this->container->get('security.token_storage')->getToken()->getUser();
            $em = $this->getDoctrine()->getManager();
            $reclamations = $em->getRepository('HuntBundle:Reclamation')->findAll();

            return $this->render('HuntBundle:Backend:reclamationsBack.html.twig'
                    ,array(
                            'reclamations' => $reclamations,
                            'user' => $user
                    ));
        }

        public function supprimerRecAction($id,Request $request)
        {

            $user = $this->container->get('security.token_storage')->getToken()->getUser();


            $forum = $this->getDoctrine()->getRepository('HuntBundle:Reclamation')->find($id);
            // var_dump($forum);

            $em =$this->getDoctrine()->getManager();
            $em->remove($forum);
            $em->flush();
            $request->getSession()
                    ->getFlashBag()
                    ->add('success', 'Reclamation a été supprimer avec succées ...!');

            return $this->redirectToRoute('rec_backend');
        }

        public function traiteRecAction($id,Request $request)
        {

            $user = $this->container->get('security.token_storage')->getToken()->getUser();



            $forum = $this->getDoctrine()->getRepository('HuntBundle:Reclamation')->find($id);
            // var_dump($forum);
            $forum-> setTraite(true);

            $em =$this->getDoctrine()->getManager();
            $em->persist($forum);
            $em->flush();

            $request->getSession()
                    ->getFlashBag()
                    ->add('success', 'Reclamation a été traité avec succées ...!');

            return $this->redirectToRoute('rec_backend');
        }

    }
