<?php

namespace App\Controller;

use App\Repository\BookRepository;
use App\Repository\RevueRepository;
use App\Repository\StudentRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class HomeController extends AbstractController
{
    #[Route('/', name: 'app_home')]
    public function index(
        BookRepository    $bookRepository,
        RevueRepository   $revueRepository,
        StudentRepository $studentRepository
    ): Response {
        return $this->render('home/index.html.twig', [
            'latestBooks'   => $bookRepository->findBy([], ['id' => 'DESC'], 4),
            'latestRevues'  => $revueRepository->findBy([], ['id' => 'DESC'], 4),
            'totalBooks'    => $bookRepository->count([]),
            'totalRevues'   => $revueRepository->count([]),
            'totalStudents' => $studentRepository->count([]),
        ]);
    }
}
