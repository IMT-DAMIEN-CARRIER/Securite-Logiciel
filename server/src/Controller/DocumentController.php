<?php

namespace App\Controller;

use App\Entity\Document;
use App\Entity\User;
use App\Form\DocumentType;
use App\Repository\DocumentRepository;
use Doctrine\ORM\EntityManagerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class DocumentController extends AbstractController
{
    /**
     * @Route(
     *     "/documents",
     *     name="documents_index"
     * )
     *
     * @IsGranted("ROLE_SUPER_ADMIN")
     *
     * @param DocumentRepository $documentRepository
     *
     * @return Response
     */
    public function index(DocumentRepository $documentRepository): Response
    {
        $documents = $documentRepository->findAll();

        return $this->render(
            'document/index.html.twig',
            [
                'documents' => $documents,
            ]
        );
    }

    /**
     * Permet de créer un document.
     *
     * @Route(
     *     "/documents/new",
     *     name="documents_create"
     * )
     *
     * @IsGranted("ROLE_SUPER_ADMIN")
     *
     * @param Request                $request
     * @param EntityManagerInterface $entityManager
     *
     * @return Response
     */
    public function create(Request $request, EntityManagerInterface $entityManager): Response
    {
        $newDocument = new Document();

        $formNewDocument = $this->createForm(
            DocumentType::class,
            $newDocument,
            ['buttonLabel' => 'Créer le document']
        );

        $formNewDocument->handleRequest($request);

        if ($formNewDocument->isSubmitted() && $formNewDocument->isValid()) {
            /** @var User $user */
            $user = $this->getUser();

            $newDocument->setUser($user);
            $entityManager->persist($newDocument);
            $entityManager->flush();

            $this->addFlash(
                'success',
                'Le document <strong>'.$newDocument->getName().'</strong> a bien été enregistré !'
            );

            return $this->redirectToRoute('documents_show', ['slug' => $newDocument->getSlug()]);
        }

        return $this->render(
            'document/new.html.twig',
            [
                'formNewDocument' => $formNewDocument->createView(),
            ]
        );
    }

    /**
     * @Route(
     *     "/documents/{slug}",
     *     name="documents_show"
     * )
     *
     * @IsGranted("ROLE_SUPER_ADMIN")
     *
     * @param Document $document
     *
     * @return Response
     */
    public function show(Document $document): Response
    {
        return $this->render(
            'document/show.html.twig',
            [
                'document' => $document,
            ]
        );
    }
}
