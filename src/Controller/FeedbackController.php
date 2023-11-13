<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\Feedback;
use Symfony\Component\HttpFoundation\Request;
use App\Form\FeedbackType;

class FeedbackController extends AbstractController
{
    #[Route('/feedback/{productName}', name: 'feedback')]
    public function feedback(Request $request, string $productName): Response

    {    

        $feedback = new Feedback();
        $form = $this->createForm(FeedbackType::class, $feedback);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            return $this->render('feedback/result.html.twig', [
                'feedback' =>   $feedback,
                'productName' => $productName,
            ]);
        }

        return $this->render('feedback/index.html.twig', [
            'form' => $form->createView(),
            'productName' => $productName,
        ]);
    }
    #[Route('/products', name: 'products')]
    public function index(): Response
    {
        $products = [
            // Créez une liste de produits en dur
            ['name' => 'Produit 1', 'price' => 100],
            ['name' => 'Produit 2', 'price' => 200],
            ['name' => 'Produit 3', 'price' => 200],
            ['name' => 'Produit 4', 'price' => 200],
            ['name' => 'Produit 4', 'price' => 200],
        ];

        return $this->render('feedback/products.html.twig', [
            'products' => $products,
        ]);
    }
    
}
