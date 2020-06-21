<?php

namespace HunterBundle\Controller;

use HunterBundle\Entity\Livraison;
use HunterBundle\Form\LivraisonType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

class LivraisonController extends Controller
{
    public function addlivraisonAction(Request $request,SessionInterface $session){
        $livraison = new Livraison();
        $form= $this->createForm(LivraisonType::class,$livraison);
        $form -> handleRequest($request);
        if ($form -> isSubmitted()){
            $em = $this->getDoctrine()->getManager();
            $em->persist($livraison);
            $em->flush();
        }

        $em= $this->getDoctrine()->getManager();
        $liv=$em->getRepository("HunterBundle:Livraison")->findAll();

        $panier = $session->get('panier2',[]);
        $panierwithdata = [];

        foreach ($panier as $id => $quantity){
            $em = $this->getDoctrine()->getManager();
            $product= $em->getRepository("HunterBundle:Product");
            $panierwithdata[] = [
                'product' => $product->find($id),
                'quantity'=> $quantity
            ];
        }
        $total = 0;
        foreach ($panierwithdata as $item){
            $totalitem = $item['product']->getPrice() * $item['quantity'];
            $total += $totalitem;
        }



        return $this->render("@Hunter/Product/livraison.html.twig",

            array('f'=>$form->createView(),
            'liv'=>$liv,
                'items'=> $panierwithdata,
                'total'=>$total
            ));
    }

    public function removeadresseAction($id){
        $em=$this->getDoctrine()->getManager();
        $produit=$em->getRepository('HunterBundle:Livraison')->find($id);
        $em->remove($produit);
        $em->flush();

        return $this->redirectToRoute('hunter_livraison');
    }



}
