<?php

namespace App\Controller;

use App\Entity\Category;
use App\Entity\Properties;
use App\Form\PropertiesType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\Persistence\ManagerRegistry as PersistenceManagerRegistry;
use Symfony\Component\HttpFoundation\Request;


class PropertiesController extends AbstractController
{
    #[Route('/properties/{id}', name: 'app_properties')]
    public function index($id, Request $request, PersistenceManagerRegistry $doctrine): Response
    {
        $Properties = new Properties();
        $formProperties = $this->createForm(PropertiesType::class, $Properties);
        $formProperties->handleRequest($request);
        $em = $doctrine->getManager();
        $queryProperties = $em->getRepository(Properties::class)->findBy(['category' => $id]);

        if ($formProperties->isSubmitted() && $formProperties->isValid()) {

            $QP = $em->getRepository(Category::class)->find($id);
            $Properties->setCategory($QP);

            $em = $doctrine->getManager();
            $em->persist($Properties);
            $em->flush();
            $this->addFlash('msg', PropertiesType::SUCCESS);
            return $this->redirectToRoute('app_properties', [
                'id' => $id,
            ]);
        }


        return $this->render('properties/index.html.twig', [
            'gid' => $id,
            'formProperties' => $formProperties->createView(),
            'postProperties' => $queryProperties,
        ]);
    }


    #[Route('properties/delet/{id}/{Gid}', name: 'FilterView')]
    public function Remove($id, $Gid, PersistenceManagerRegistry $doctrine)
    {
        $em = $doctrine->getManager();
        $entity = $em->getRepository(Properties::class)->findOneBy(array('id' => $id));

        if ($entity != null) {
            $em->remove($entity);
            $em->flush();
        }
        $this->addFlash('msg', PropertiesType::DELETED);
        return $this->redirectToRoute('app_properties', [
            'id' => $Gid,
        ]);
    }


    #[Route('properties/edit/{id}/{name}/{Gid}', name: 'EditCategory')]
    public function update($Gid, Request $request, int $id, $name, PersistenceManagerRegistry $doctrine): Response
    {
        $Properties = new Properties();
        // $formProperties = $this->createForm(PropertiesType::class, $Properties, array('code' => 'hola', 'name' => 'hola'));
        $formProperties = $this->createForm(PropertiesType::class, $Properties);
        $Properties->setName('jhon');
        // $formProperties->setDefined('ticket');

        $formProperties->handleRequest($request);
        $em = $doctrine->getManager();
        $QP = $em->getRepository(Properties::class)->find($id);

        if ($QP != null) {
            if ($formProperties->isSubmitted() && $formProperties->isValid()) {

                $QP->setCode($formProperties['code']->getData());
                $QP->setName($formProperties['name']->getData());
                $QP->setDescription($formProperties['description']->getData());
                $QP->setBrand($formProperties['brand']->getData());
                $QP->setPrice($formProperties['price']->getData());
                $em->flush();
                $this->addFlash('msg', PropertiesType::UPDATE_SU);
                return $this->redirectToRoute('app_properties', [
                    'id' => $Gid,
                ]);
            }
        } else {
            $this->addFlash('msg', PropertiesType::UPDATE_EX);
            return $this->redirectToRoute('app_properties', [
                'id' => $Gid,
            ]);
        }
        return $this->render('e.html.twig', [
            'data' => $QP,
            'name' => $name,
            'formProperties' => $formProperties->createView(),
        ]);
    }
}
