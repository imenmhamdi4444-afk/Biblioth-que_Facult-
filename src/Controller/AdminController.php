<?php

namespace App\Controller;

use App\Repository\BookRepository;
use App\Repository\BorrowingRepository;
use App\Repository\RevueRepository;
use App\Repository\StudentRepository;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class AdminController extends AbstractController
{
    #[Route('/admin', name: 'admin_dashboard')]
    #[IsGranted('ROLE_ADMIN')]
    public function dashboard(
        BookRepository     $bookRepository,
        RevueRepository    $revueRepository,
        StudentRepository  $studentRepository,
        BorrowingRepository $borrowingRepository
    ): Response {
        $overdue  = $borrowingRepository->findOverdueBorrowings();
        $active   = $borrowingRepository->findActiveBorrowings();

        return $this->render('admin/dashboard.html.twig', [
            'totalBooks'       => $bookRepository->count([]),
            'totalRevues'      => $revueRepository->count([]),
            'totalStudents'    => $studentRepository->count([]),
            'totalBorrowings'  => $borrowingRepository->count([]),
            'activeBorrowings' => count($active),
            'overdueBorrowings'=> count($overdue),
            'overdueList'      => $overdue,
        ]);
    }
}
