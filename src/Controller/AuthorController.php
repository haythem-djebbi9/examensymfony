<?php

namespace App\Controller;

use App\Entity\Author;
use App\Form\AuthorType;
use App\Repository\AuthorRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class AuthorController extends AbstractController
{
    #[Route('/author', name: 'app_author')]
    public function index(): Response
    {
        return $this->render('author/index.html.twig', [
            'controller_name' => 'AuthorController',
        ]);
    }

    #[Route("/author/getall", name: 'app_author_getall')]
    public function affiche(AuthorRepository $repository): Response
    {
        $authors = $repository->findAll();
        return $this->render('author/index.html.twig', ['authors' => $authors]);
    }

    #[Route("/author/add", name: 'app_author_add')]
    public function add(Request $request, EntityManagerInterface $em): Response
    {
        $author = new Author();

        $form = $this->createForm(AuthorType::class, $author);
        $form->add('ajouter', SubmitType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->persist($author);
            $em->flush();

            return $this->redirectToRoute('app_author_getall');
        }

        return $this->render('author/add.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route("/author/edit/{id}", name: 'app_author_edit')]
    public function edit(Request $request, EntityManagerInterface $em, AuthorRepository $repository, int $id): Response
    {
        $author = $repository->find($id);
        if (!$author) {
            throw $this->createNotFoundException("L'auteur avec l'id $id n'existe pas.");
        }

        $form = $this->createForm(AuthorType::class, $author);
        $form->add('modifier', SubmitType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->flush();

            return $this->redirectToRoute('app_author_getall');
        }

        return $this->render('author/edit.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route("/author/delete/{id}", name: 'app_author_delete')]
    public function delete(EntityManagerInterface $em, AuthorRepository $repository, int $id): Response
    {
        $author = $repository->find($id);
        if (!$author) {
            throw $this->createNotFoundException("L'auteur avec l'id $id n'existe pas.");
        }

        $em->remove($author);
        $em->flush();

        return $this->redirectToRoute('app_author_getall');
    }
}
