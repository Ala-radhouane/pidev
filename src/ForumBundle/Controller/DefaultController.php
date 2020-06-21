<?php

namespace ForumBundle\Controller;

use ForumBundle\Entity\Commentaire;
use ForumBundle\Entity\publication;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class DefaultController extends Controller
{

    public function indexAction(Request $request)
    {
        $user = $this->container->get('security.token_storage')->getToken()->getUser();
        $em = $this->getDoctrine()->getManager();
        $pubs = $em->getRepository('ForumBundle:publication')->findAll();

        if ($request->isMethod('POST')) {

            $c = array() ;

            foreach ($pubs as $p)
            {
               if(strstr(strtolower($p->getTypeChasse()),$request->get('search')))  array_push($c,$p);
            }
           // var_dump(strstr($c));

            return $this->render('ForumBundle:Default:index.html.twig'
                ,array(
                    'pubs' => $c,
                    // 'count' => $count
                ));
        }

        return $this->render('ForumBundle:Default:index.html.twig'
            ,array(
                'pubs' => $pubs,
                'user' => $user
            ));
    }

    public function modifierforumAction($id,Request $request)
    {
        $user = $this->container->get('security.token_storage')->getToken()->getUser();
        $em = $this->getDoctrine()->getManager();
        $pubs = $em->getRepository('ForumBundle:publication')->find($id);
       if ($request->isMethod('POST')) {

           $pubs->setDescription($request->get('description'));
           $pubs->setTypeChasse($request->get('type'));



           // validation

           $error = false ;
           $error_str='';

           if( strlen($pubs->getTypeChasse()) == 0) {
               $error=true;
               $error_str='Veuillez remplir toutes les champs';
           }


           if   ( strlen($pubs->getDescription()) == 0) {
               $error=true;
               $error_str='Veuillez remplir toutes les champs';
           }

           if($error==true) {

               $request->getSession()
                   ->getFlashBag()
                   ->add('error', $error_str);

               $url = $this->generateUrl('forum_modifier', ['id' => $id]);
               return $this->redirect($url);
           }


           $em = $this->getDoctrine()->getManager();
           $em->persist($pubs);
           $em->flush();

           $request->getSession()
               ->getFlashBag()
               ->add('success', 'Forum modifier avec succées ...!');

           $url = $this->generateUrl('forum_homepage');
           return $this->redirect($url);
       }

        return $this->render('ForumBundle:Default:modifForum.html.twig' ,array(
            'p'=>$pubs
            )
        );
    }

    public function detailsAction($id,Request $request)
    {
        $user = $this->container->get('security.token_storage')->getToken()->getUser();
        $em = $this->getDoctrine()->getManager();
        $pubs = $em->getRepository('ForumBundle:publication')->find($id);
        $comments = $em->getRepository('ForumBundle:Commentaire')->findBy([
            "idpub"=>$id
        ]);
        $count = count($comments);

        if ($request->isMethod('POST')) {

            $commentaire = new Commentaire();
            $commentaire->setCommentaire($request->get('Commentaire'));
            $commentaire->setIdpub($id);
            $commentaire->setIdUser($user);
            $commentaire->setDate(new \DateTime("now"));


            // validation

            $error = false ;
            $error_str='';

            if( strlen($commentaire->getCommentaire()) == 0) {
                $error=true;
                $error_str='Commentaire vide';
            }

            if($error==true) {

                $request->getSession()
                    ->getFlashBag()
                    ->add('error', $error_str);

                $url = $this->generateUrl('forum_details', ['id' => $id]);
                return $this->redirect($url);
            }

            $em = $this->getDoctrine()->getManager();
            $em->persist($commentaire);
            $em->flush();

            $request->getSession()
                ->getFlashBag()
                ->add('success', 'Commentaire Ajouté avec succées ...!');


            $url = $this->generateUrl('forum_details', ['id' => $id]);

            return $this->redirect($url);
        }

      //  var_dump($comments);

        return $this->render('ForumBundle:Default:Details.html.twig'
            ,array(
                'p' => $pubs,
                'count' => $count,
                'comments'=>$comments,
                'user'=>$user
            ));
    }

    public function supprimerforumAction($id,Request $request)
    {

        $user = $this->container->get('security.token_storage')->getToken()->getUser();


        $forum = $this->getDoctrine()->getRepository('ForumBundle:publication')->find($id);
        // var_dump($forum);

        $em =$this->getDoctrine()->getManager();
        $em->remove($forum);
        $em->flush();

        $request->getSession()
            ->getFlashBag()
            ->add('success', 'forum a été supprimer avec succées ...!');

        $url = $this->generateUrl('forum_homepage');
        return $this->redirect($url);
    }

    public function supprimercommentAction($id,Request $request)
    {

        $user = $this->container->get('security.token_storage')->getToken()->getUser();


        $comment = $this->getDoctrine()->getRepository('ForumBundle:Commentaire')->find($id);
        // var_dump($forum);

        $em =$this->getDoctrine()->getManager();
        $em->remove($comment);
        $em->flush();
        $request->getSession()
            ->getFlashBag()
            ->add('success', 'Commentaire a été supprimer avec succées ...!');

        $url = $this->generateUrl('forum_details', ['id' => $comment->getIdpub()]);
        return $this->redirect($url);
    }

    public function ajouterforumAction(Request $request)
    {
        $user = $this->container->get('security.token_storage')->getToken()->getUser();
        $em = $this->getDoctrine()->getManager();

    if ($request->isMethod('POST')) {

        $error = true;

        $forum = new publication();
        $forum->setDescription($request->get('description'));
        $forum->setTypeChasse($request->get('type'));
        $forum->setIdUser($user);
        $forum->setResolu(0);

        // validation

        $error = false ;
        $error_str='';

        if( strlen($forum->getTypeChasse()) == 0) {
            $error=true;
            $error_str='Veuillez remplir toutes les champs';
        }


        if   ( strlen($forum->getDescription()) == 0) {
            $error=true;
            $error_str='Veuillez remplir toutes les champs';
        }

        if($error==true) {

            $request->getSession()
                ->getFlashBag()
                ->add('error', $error_str);

            $url = $this->generateUrl('forum_ajouter');
            return $this->redirect($url);
        }

        $em = $this->getDoctrine()->getManager();
        $em->persist($forum);
        $em->flush();

        $request->getSession()
            ->getFlashBag()
            ->add('success', 'Forum Ajouté avec succées ...!');

        $url = $this->generateUrl('forum_homepage');
        return $this->redirect($url);
    }

        return $this->render('ForumBundle:Default:ajouter.html.twig'
            );
    }

    public function modifiercommentAction($id,Request $request)
    {
        $user = $this->container->get('security.token_storage')->getToken()->getUser();
        $em = $this->getDoctrine()->getManager();
        $comment = $em->getRepository('ForumBundle:Commentaire')->find($id);


        if ($request->isMethod('POST')) {


            $comment->setCommentaire($request->get('Commentaire'));
            $comment->setDate(new \DateTime("now"));

            $error = false ;
            $error_str='';

            if( strlen($comment->getCommentaire()) == 0) {
                $error=true;
                $error_str='Commentaire vide';
            }

            if($error==true) {

                $request->getSession()
                    ->getFlashBag()
                    ->add('error', $error_str);

                $url = $this->generateUrl('com_modifier' , ['id' => $id]);
                return $this->redirect($url);
            }

            $em = $this->getDoctrine()->getManager();
            $em->persist($comment);
            $em->flush();

            $request->getSession()
                ->getFlashBag()
                ->add('success', 'Commentaire modifier avec succées ...!');

            $url = $this->generateUrl('forum_details', ['id' => $comment->getIdpub()]);
            return $this->redirect($url);
        }

        return $this->render('ForumBundle:Default:modifierComment.html.twig'
            ,array(
                'comment' => $comment,
            ));
    }

    public function resoluAction($id,Request $request)
    {

        $user = $this->container->get('security.token_storage')->getToken()->getUser();
        $pub = $this->getDoctrine()->getRepository('ForumBundle:publication')->find($id);
        // var_dump($forum);

        $pub->setResolu(true);

        $em =$this->getDoctrine()->getManager();
        $em->persist($pub);
        $em->flush();
        $request->getSession()
            ->getFlashBag()
            ->add('success', 'Forum a été résolu avec succées ...!');

        $url = $this->generateUrl('forum_details', ['id' => $id]);
        return $this->redirect($url);
    }



    public function backForumsAction(Request $request)
    {
        $user = $this->container->get('security.token_storage')->getToken()->getUser();
        $em = $this->getDoctrine()->getManager();
        $pubs = $em->getRepository('ForumBundle:publication')->findAll();

            return $this->render('ForumBundle:Default:forumsBack.html.twig'
                ,array(
                    'pubs' => $pubs,
                    'user'=>$user
                    // 'count' => $count
                ));
    }

    public function evaluerAction($id,Request $request)
    {
        $user = $this->container->get('security.token_storage')->getToken()->getUser();
        $em = $this->getDoctrine()->getManager();
        $pubs = $em->getRepository('ForumBundle:publication')->find($id);

        $comments = $em->getRepository('ForumBundle:Commentaire')->findBy([
            "idpub"=>$id
        ]);
        $count = count($comments);

        if ($request->isMethod('POST')) {
            $note = $request->get('rate');
            $pubs->setNote($note);

            $em =$this->getDoctrine()->getManager();
            $em->persist($pubs);
            $em->flush();

            $request->getSession()
                ->getFlashBag()
                ->add('success', 'Note enregistré avec succées ...!');

            $url = $this->generateUrl('forum_homepage');
            return $this->redirect($url);
        }

        return $this->render('ForumBundle:Default:evaluer.html.twig'
            ,array(
                'p' => $pubs,
                'count' => $count,
                'comments'=>$comments,
                'user'=>$user
            ));
    }

    public function resoluBackAction($id,Request $request)
    {

        $user = $this->container->get('security.token_storage')->getToken()->getUser();
        $pub = $this->getDoctrine()->getRepository('ForumBundle:publication')->find($id);
        // var_dump($forum);

        $pub->setResolu(true);

        $em =$this->getDoctrine()->getManager();
        $em->persist($pub);
        $em->flush();
        $request->getSession()
            ->getFlashBag()
            ->add('success', 'Forum a été résolu avec succées ...!');

        $url = $this->generateUrl('forum_back');
        return $this->redirect($url);
    }

    public function supprimerBackAction($id,Request $request)
    {

        $user = $this->container->get('security.token_storage')->getToken()->getUser();


        $pub = $this->getDoctrine()->getRepository('ForumBundle:publication')->find($id);
        // var_dump($forum);

        $em =$this->getDoctrine()->getManager();
        $em->remove($pub);
        $em->flush();
        $request->getSession()
            ->getFlashBag()
            ->add('success', 'Forum a été supprimer avec succées ...!');

        $url = $this->generateUrl('forum_back');
        return $this->redirect($url);
    }
}
