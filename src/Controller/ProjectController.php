<?php

namespace App\Controller;

use App\Entity\Project;
use App\Repository\ProjectRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;


#[Route('/api', name: 'api_')]
class ProjectController extends AbstractController
{
    #[Route('/project', name: 'api_project')]
    public function index(ProjectRepository $project): JsonResponse
    {
        $all = $project->findAll();
        return $this->json( [
            'controller_name' => $all,
        ]);
    }
    #[Route('/project/new', name: 'api_project_new')]
    public function new(ManagerRegistry $doctrine,Request $request): JsonResponse
    {
        $entityManager = $doctrine->getManager();
   
        $project = new Project();
        $project->setName($request->request->get('name'));
        $project->setDescription($request->request->get('description'));
   
        $entityManager->persist($project);
        $entityManager->flush();
   
        return $this->json('Created new project successfully with id ' . $project->getId());
    }
    #[Route('/project/show/{id}', name: 'api_project_show')]
    public function show(ProjectRepository $project ,int $id): JsonResponse
    {
        $element = $project->find($id);
        
        if (!$element) {
            return $this->json("Aucun utilisateur trouvé avec l'id " . $id, 404);
        }
         
   
        return $this->json($element);
    }
    #[Route('/project/edit/{id}', name: 'api_project_edit')]
    public function edit(ProjectRepository $project ,int $id,Request $req,ManagerRegistry $doctrine): JsonResponse
    {
        $em = $doctrine->getManager();
        $element = $project->find($id);

        if (!$element) {
            return $this->json("Aucun utilisateur trouvé avec l'id" . $id, 404);
        }
        $content = json_decode($req->getContent());
        $element->setName($req->request->get('name'));
        $element->setDescription($req->request->get('description'));
        $em->flush();
   
        $data =  [
            'id' => $element->getId(),
            'name' => $element->getName(),
            'description' => $element->getDescription(),
        ];
           
        return $this->json($data);        
    }
    #[Route('/project/delete/{id}', name: 'api_project_delete')]
    public function delete(ProjectRepository $project ,int $id,ManagerRegistry $doctrine): JsonResponse
    {
        $element = $project->find($id);
        $em = $doctrine->getManager();

        if (!$element) {
            return $this->json("Aucun utilisateur trouvé avec l'id " . $id, 404);
        }
        $em->remove($element);
        $em->flush();
   
        return $this->json("Element supprimé avec succès");
    }
}
