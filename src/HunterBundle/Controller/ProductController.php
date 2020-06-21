<?php

namespace HunterBundle\Controller;


use HunterBundle\Entity\Product;
use HunterBundle\Repository\ProductRepository;
use ProduitBundle\Form\ProduitType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use HunterBundle\Form\ProductType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;
use Symfony\Component\HttpFoundation\JsonResponse;

class ProductController extends Controller
{
    public  function addproductAction(Request $request){
        $Product = new Product();
        $form= $this->createForm(ProductType::class,$Product);
        $form -> handleRequest($request);
        if ($form -> isSubmitted() && $form ->isValid()){
            $em = $this->getDoctrine()->getManager();
            $Product->uploadProductPicture();
            $em->persist($Product);
            $em->flush();
            $serializer = new Serializer([new ObjectNormalizer()]);
            $formatted = $serializer->normalize($Product);
            return new JsonResponse($formatted);
        }
        return $this->render("@Hunter/Product/addproduct.html.twig",array('form'=>$form->createView()));
    }

    public function viewproduitAction(Request $request){
        $em= $this->getDoctrine()->getManager();
        $products=$em->getRepository("HunterBundle:Product")->findAll();
        $serializer = new Serializer([new ObjectNormalizer()]);
        $formatted = $serializer->normalize($products);
        return new JsonResponse($formatted);
        $paginator= $this->get('knp_paginator');
        $result=$paginator->paginate(
            $products,
            $request->query->getInt('page', 1), /*page number*/
            $request->query->getInt('limit',4) /*limit per page*/
        );
        return $this->render("@Hunter/Product/listeproduct.html.twig",array('products'=>$result));

    }

    public function editproductAction(Request $request,$id){
        $em=$this->getDoctrine()->getManager();
        $product=$em->getRepository('HunterBundle:Product')->find($id);

        $form= $this->createForm(ProductType::class,$product);
        $form->handleRequest($request);

        if( $form->isSubmitted() ){
            $em=$this->getDoctrine()->getManager();
            $product->uploadProductPicture();
            $em->persist($product);
            $em->flush();
            return $this->redirectToRoute('hunter_afficher');
        }

        return $this->render('@Hunter\Product\editproduct.html.twig',array('form'=>$form->createView()));
    }

    public function deleteproduitAction($id){
        $em=$this->getDoctrine()->getManager();
        $produit=$em->getRepository('HunterBundle:Product')->find($id);
        $em->remove($produit);
        $em->flush();
        $serializer = new Serializer([new ObjectNormalizer()]);
        $formatted = $serializer->normalize($produit);
        return new JsonResponse($formatted);

        return $this->redirectToRoute('hunter_afficher');
    }

    public function produitsHorsStockAction(Request $request){
        $em=$this->getDoctrine()->getManager();

        $produits=$em->getRepository('HunterBundle:Product')->findProduitsHorsStock();
        $paginator= $this->get('knp_paginator');
        $result=$paginator->paginate(
            $produits,
            $request->query->getInt('page', 1), /*page number*/
            $request->query->getInt('limit',5) /*limit per page*/
        );
        return $this->render('@Hunter/Product/produitsHorsStock.html.twig',array('products'=>$result));
    }


    public function produitsEnReptureDeStockAction(Request $request){
        $em=$this->getDoctrine()->getManager();

        $produits=$em->getRepository('HunterBundle:Product')->findProduitsEnReptureDeStock();
        $paginator= $this->get('knp_paginator');
        $result=$paginator->paginate(
            $produits,
            $request->query->getInt('page', 1), /*page number*/
            $request->query->getInt('limit',5) /*limit per page*/
        );
        return $this->render('@Hunter/Product/produitsEnReptureDeStock.html.twig',array('products'=>$result));
    }

    public  function ShopAction(){
        $em=$this->getDoctrine()->getManager();
        $products=$em->getRepository("HunterBundle:Product")->findAll();
        $serializer = new Serializer([new ObjectNormalizer()]);
        $formatted = $serializer->normalize($products);
        return new JsonResponse($formatted);


        return $this->render("@Hunter/Product/shop.html.twig",array('products'=>$products));

    }

    public function DetailAction($id){
        $em = $this->getDoctrine()->getManager();
        $product= $em->getRepository("HunterBundle:Product")->find($id);
        return $this->render("@Hunter/Product/detail.html.twig",array('product'=>$product));

    }

    public  function  ChartdataAction(SessionInterface $session ){

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



            return $this->render("@Hunter/Product/panier.html.twig",[
                'items'=> $panierwithdata,
                'total'=>$total
            ]);

    }
    public function ChartAction($id,SessionInterface $session){

        $panier1 = $session->get('panier2',[]);
        if (!empty($panier1[$id])){
            $panier1[$id]++;
        }else { $panier1[$id] = 1 ;}

        $session->set('panier2',$panier1);



       return $this->redirectToRoute('hunter_chartdata');
    }

    public function ChartremoveAction($id,SessionInterface $session){
        $panier = $session->get('panier2',[]);
        if (!empty($panier[$id])){
            unset($panier[$id]);
        }
        $session->set('panier2',$panier);
        return $this->redirectToRoute('hunter_chartdata');
    }
}
