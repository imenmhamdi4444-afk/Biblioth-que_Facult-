<?php

namespace App\Controller;

use App\Repository\AuthorRepository;
use App\Repository\BookRepository;
use App\Repository\PublisherRepository;
use App\Repository\RevueRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class SearchController extends AbstractController
{
    #[Route('/search', name: 'app_search', methods: ['GET'])]
    public function search(
        Request           $request,
        BookRepository    $bookRepository,
        RevueRepository   $revueRepository,
        AuthorRepository  $authorRepository,
        PublisherRepository $publisherRepository
    ): Response {
        $keyword      = trim($request->query->get('q', $request->query->get('keyword', '')));
        $type         = $request->query->get('type', 'all');       // all | book | revue
        $authorId     = (int) $request->query->get('author', 0);
        $publisherId  = (int) $request->query->get('publisher', 0);
        $availableOnly = (bool) $request->query->get('available', false);

        $books  = [];
        $revues = [];

        $hasQuery = $keyword !== '' || $authorId || $publisherId || $availableOnly;

        if ($hasQuery) {
            if ($type === 'all' || $type === 'book') {
                $books = $bookRepository->searchWithFilters(
                    $keyword,
                    $authorId ?: null,
                    $publisherId ?: null,
                    $availableOnly
                );
            }

            if ($type === 'all' || $type === 'revue') {
                $revues = $revueRepository->searchWithFilters(
                    $keyword,
                    $authorId ?: null,
                    $publisherId ?: null,
                    $availableOnly
                );
            }
        }

        return $this->render('search/index.html.twig', [
            'keyword'      => $keyword,
            'type'         => $type,
            'authorId'     => $authorId,
            'publisherId'  => $publisherId,
            'availableOnly'=> $availableOnly,
            'books'        => $books,
            'revues'       => $revues,
            'totalResults' => count($books) + count($revues),
            'hasQuery'     => $hasQuery,
            'authors'      => $authorRepository->findBy([], ['name' => 'ASC']),
            'publishers'   => $publisherRepository->findBy([], ['raisonSociale' => 'ASC']),
        ]);
    }
}
