<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\Feedback;
use Symfony\Component\HttpFoundation\Request;
use App\Form\FeedbackType;
use App\Repository\FeedbackRepository;
use Doctrine\ORM\EntityManagerInterface;


class FeedbackController extends AbstractController
{   

    private FeedbackRepository $feedbackRepository;

    public function __construct(FeedbackRepository $feedbackRepository)
    {
        $this->feedbackRepository = $feedbackRepository;
    }

    #[Route('/', name:'index')]
    public function index (): Response {
        return $this->render('feedback/index.html.twig');
    }

    #[Route('/feedback/{productName}', name: 'feedback')]
    public function feedback(Request $request, EntityManagerInterface $entityManager, string $productName): Response
    {    
        $feedback = new Feedback();
        $form = $this->createForm(FeedbackType::class, $feedback);
        $form->handleRequest($request);
    
        if ($form->isSubmitted() && $form->isValid()) {
            
            $entityManager->persist($feedback); 
            $entityManager->flush();
    
            
            return $this->render('feedback/result.html.twig', [
                'feedback' =>   $feedback,
                'productName' => $productName,
            ]);
        }
    
        
        return $this->render('feedback/form.html.twig', [
            'form' => $form->createView(),
            'productName' => $productName,
        ]);
    }
    #[Route('/products', name: 'products')]
    public function productList(): Response
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

    #[Route('/feedbacks', name: 'feedback_list')]
    public function feedbackList(FeedbackRepository $feedbackRepository): Response
    {
        $feedbacks = $feedbackRepository->findAll();

        $feedbackData = [];
        foreach ($feedbacks as $feedback) {
            $feedbackData[] = [
                'id' => $feedback->getID(),
                'clientName' => $feedback->getNomClient(),
                'email' => $feedback->getEmailClient(),
                'note' => $feedback->getNoteProduit(),
                'comment' => $feedback->getCommentaires(),
                'submissionDate' => $feedback->getDateSoumission()->format('Y-m-d H:i:s'),
            ];
        }

        return $this->render('feedback/feedback_list.html.twig', [
            'feedbackData' => $feedbackData,
        ]);
    }

    #[Route('/feedback/edit/{id}', name: 'feedback_edit')]
    public function edit(Request $request, EntityManagerInterface $entityManager, int $id): Response
    {
        $feedback = $entityManager->getRepository(Feedback::class)->find($id);

        if (!$feedback) {
            throw $this->createNotFoundException('Aucun feedback trouvé pour id '.$id);
        }

        $form = $this->createForm(FeedbackType::class, $feedback);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();
            return $this->redirectToRoute('feedback_list'); 
        }

        return $this->render('feedback/edit.html.twig', [
            'form' => $form->createView(),
        ]);
    }



    #[Route('/feedback/{id}/delete', name: 'feedback_delete', methods: ['POST'])]
    public function delete(Feedback $feedback): Response
    {
        $feedbackRepository = $this->feedbackRepository;
        $feedbackRepository->delete($feedback);

        return $this->redirectToRoute('feedback_list');
    }

    
    
}
