<?php

namespace App\Controller;

use App\Entity\Category;
use App\Form\CategoryType;
use App\Repository\CategoryRepository;
use Cocur\Slugify\Slugify;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class CategoryController extends AbstractController
{

    // Si on veut passer une instance de CategoryRepository sans déclarer de globals dans twig.yaml il faut créer une méthode 
    // de controller accessible par la fonction render(controller()) dans twig (pas optimiser, choisir la méthode des globals)
    // protected $categoryRepository;

    // public function __construct(CategoryRepository $categoryRepository){
    //     $this->categoryRepository = $categoryRepository;
    // }


    public function renderMenuList(){
        // 1. Aller chercher les catégorie existantes dans la base de données
        $category = $this->categoryRepository->findAll();

        // 2. Faire un rendu HTML sous forme de Response ($this->render)
        return $this->render("Shared/_navbar_category.html.twig", [
            "category"=>$category
        ]);
    }


    /**
     * @Route("/admin/category/create", name="category_create")
     */
    public function create(Request $request, Slugify $slugify, EntityManagerInterface $em): Response
    {

        $category = new Category;
        $form = $this->createForm(CategoryType::class, $category);

        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){
            $category->setSlug(strtolower($slugify->slugify($category->getName())));
            $em->persist($category);
            $em->flush();

            return $this->redirectToRoute("homepage");
        }

        $formView = $form->createView();


        return $this->render('category/create.html.twig', [
            'formView' => $formView
        ]);
    }



    /**
     *@Route("/admin/category/{id}/edit", name="category_edit")
     */
    public function edit($id, Request $request, Slugify $slugify, EntityManagerInterface $em, CategoryRepository $categoryRepository): Response{

        $category = $categoryRepository->find($id);
        $form = $this->createForm(CategoryType::class, $category);

        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){
            $category->setSlug(strtolower($slugify->slugify($category->getName())));
            $em->flush();

            return $this->redirectToRoute("homepage");
        }

        $formView = $form->createView();

        return $this->render("category/edit.html.twig", [
            "category"=>$category,
            "formView"=>$formView
        ]);
    }
}
