<?php

namespace App\Controller;

use App\Repository\BorrowingRepository;
use App\Entity\BookSearch;
use App\Form\BookSearchType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Dompdf\Dompdf;
use Dompdf\Options;

class ReportController extends AbstractController
{
    #[Route('/most-popular-book', name: 'most_popular_book')]
    #[IsGranted('ROLE_ADMIN')]
    public function index(BorrowingRepository $repository): Response
    {
        $books = $repository->findMostPopularBooks();
        return $this->render('report/index.html.twig', [
            'books' => $books,
        ]);
    }

    #[Route('/most-popular-book/pdf', name: 'most_popular_book_pdf')]
    #[IsGranted('ROLE_ADMIN')]
    public function exportPdf(BorrowingRepository $repository): Response
    {
        $books = $repository->findMostPopularBooks();

        // Configure Dompdf
        $pdfOptions = new Options();
        $pdfOptions->set('defaultFont', 'Arial');
        $dompdf = new Dompdf($pdfOptions);

        // Retrieve the HTML content
        $html = $this->renderView('report/pdf_template.html.twig', [
            'books' => $books,
            'title' => "Rapport des Livres les plus Populaires"
        ]);

        // Load HTML to Dompdf
        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4', 'portrait');
        $dompdf->render();

        // Output the generated PDF to Browser (force download)
        $output = $dompdf->output();
        
        return new Response($output, 200, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'attachment; filename="rapport_populaire.pdf"'
        ]);
    }

    #[Route('/borrowing-book', name: 'borrowing_book')]
    #[IsGranted('ROLE_ADMIN')]
    public function BorrowingBook(Request $request, BorrowingRepository $repository): Response
    {
        $bookSearch = new BookSearch();
        $form = $this->createForm(BookSearchType::class, $bookSearch);
        $form->handleRequest($request);
        $borrowings = [];

        if ($form->isSubmitted() && $form->isValid()) {
            $book = $bookSearch->getBook();
            if ($book != "")
                $borrowings = $repository->findBy(array('book' => $book));
            else
                $borrowings = $repository->findAll();
        }

        return $this->render('report/BorrowingBook.html.twig', [
            'form' => $form->createView(),
            'borrowings' => $borrowings
        ]);
    }
}