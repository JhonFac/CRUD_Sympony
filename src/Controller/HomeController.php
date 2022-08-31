<?php

namespace App\Controller;

use App\Entity\Category;
use App\Entity\Properties;
use App\Form\CategoryType;
use App\Form\PropertiesType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\Persistence\ManagerRegistry as PersistenceManagerRegistry;
use Knp\Component\Pager\PaginatorInterface;

class HomeController extends AbstractController
{

    #[Route('/', name: 'index')]
    public function myRedirectAction(Request $request)
    {
        return $this->redirectToRoute('app_home');
    }

    function PaginationTable($em, $paginator, $request)
    {
        $query = $em->getRepository(Category::class)->FindFields();
        $pagination = $paginator->paginate(
            $query, /* query NOT result */
            $request->query->getInt('page', 2), /*page number*/
            5 /*limit per page*/
        );
        return $pagination;
    }

    #[Route('/home', name: 'app_home')]
    public function index(Request $request, PaginatorInterface $paginator, PersistenceManagerRegistry $doctrine,): Response
    {
        $Category = new Category();
        $formCategory = $this->createForm(CategoryType::class, $Category);
        $formCategory->handleRequest($request);
        $em = $doctrine->getManager();
        $pagination = HomeController::PaginationTable($em, $paginator, $request);

        if ($formCategory->isSubmitted() && $formCategory->isValid()) {

            $entity = $em->getRepository(Category::class)->findOneBy(array('name' => $formCategory['name']->getData()));
            if ($entity == null) {
                $em->persist($Category);
                $em->flush();
                $this->addFlash('msg', CategoryType::SUCCESS);
            } else {
                $this->addFlash('msg', CategoryType::EXISTS);
            }
            return $this->redirectToRoute('app_home');
        }

        return $this->render('home/index.html.twig', [
            'pagination' => $pagination,
            'formCategory' => $formCategory->createView(),
        ]);
    }


    #[Route('/delet/{id}', name: 'FilterView')]
    public function Remove($id, PersistenceManagerRegistry $doctrine)
    {
        $em = $doctrine->getManager();
        $entity = $em->getRepository(Category::class)->findOneBy(array('id' => $id));

        if ($entity != null) {
            $em->remove($entity);
            $em->flush();
        }

        $this->addFlash('msg', CategoryType::DELETED);
        return $this->redirect('/home');
    }

    #[Route('/home/edit/{id}/{name}', name: 'EditCategory')]
    public function update(Request $request, int $id, $name, PersistenceManagerRegistry $doctrine): Response
    {
        $Category = new Category();
        $formCategory = $this->createForm(CategoryType::class, $Category);
        $em = $doctrine->getManager();
        $product = $em->getRepository(Category::class)->find($id);
        $formCategory->handleRequest($request);

        if ($product != null) {
            if ($formCategory->isSubmitted() && $formCategory->isValid()) {
                $product->setName($formCategory['name']->getData());
                $product->setActive($formCategory['active']->getData());
                $em->flush();
                $this->addFlash('msg', CategoryType::UPDATE_SU);
                return $this->redirectToRoute('app_home');
            }
        } else {
            $this->addFlash('msg', CategoryType::UPDATE_EX);
            return $this->redirectToRoute('app_home');
        }
        return $this->render('p.html.twig', [
            'name' => $name,
            'formCategory' => $formCategory->createView(),
        ]);
    }
}
