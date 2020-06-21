<?php

namespace AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class DefaultController extends Controller
{
    /**
     * @Route("/", name="homepage")
     */
    public function indexAction(Request $request)
    {
        $user = $this->container->get('security.token_storage')->getToken()->getUser();
        $em = $this->getDoctrine()->getManager();
        $produits = $em->getRepository('HuntBundle:Produit')->findAll();
        return $this->render('HuntBundle:Default:Home.html.twig'
            ,array(
                'produits' => $produits,
                //'user' => $user
            ));
    }
}
