<?php

namespace App\Controller;

use App\AccessManager\AccessBlocker;
use App\Entity\Category;
use App\Form\CategoryType;
use App\Repository\CategoryRepository;
use Cocur\Slugify\Slugify;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Security;

class CategoryController extends AbstractController
{

    // Si on veut passer une instance de CategoryRepository sans déclarer de globals dans twig.yaml il faut créer une méthode 
    // de controller accessible par la fonction render(controller()) dans twig (pas optimiser, choisir la méthode des globals)
    // protected $categoryRepository;

    // public function __construct(CategoryRepository $categoryRepository){
    //     $this->categoryRepository = $categoryRepository;
    // }


    // public function renderMenuList(){
    //     // 1. Aller chercher les catégorie existantes dans la base de données
    //     $category = $this->categoryRepository->findAll();

    //     // 2. Faire un rendu HTML sous forme de Response ($this->render)
    //     return $this->render("Shared/_navbar_category.html.twig", [
    //         "category"=>$category
    //     ]);
    // } ==> Pour passer une instance de Category à un twig via render(controller())


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
    public function edit($id, Request $request, Slugify $slugify, EntityManagerInterface $em, 
    CategoryRepository $categoryRepository, AccessBlocker $accessBlocker): Response{

        // $session = $requestStack->getSession();
        
        // $user = $security->getUser();
        // if(!$user){
        //     $session->set("messageError", "Vous devez être connecté pour accéder à cette page");
        //     $session->set("tryToConnectRoute", "/admin/category/$id/edit");
        //     return $this->redirectToRoute("security_login");
        // }

        // elseif(!in_array("ROLE_ADMIN", $user->getRoles())){
        //     throw new AccessDeniedHttpException("Vos droits sont insuffisants!");
        // }
        // ==> Equivalent

        // if(!$security->isGranted("ROLE_ADMIN")){
        //     throw new AccessDeniedHttpException("Vos droits sont insuffisants!");
        // }
        
        $category = $categoryRepository->find($id);

        if(!$category){
            //throw new NotFoundHttpException("La catégorie $slug n'existe pas"); => Alternative
            $this->session->set("messageError", "La catégorie n'existe pas");
            return $this->redirectToRoute("homepage");
        }


        // Utilisation du AccessBlocker
        //-------------------------------------------------------------------------------------------------------------------
        //$accesCondition=($this->getUser()===$category->getEditor());
        // $accesCondition=($this->isGranted("ROLE_ADMIN"));

        //$messageNoAccess = "Vous n'êtes pas le créateur de la catégorie";
        // $messageNoAccess = "Vous devez être administrateur pour accéder à cette page";
        
        // if($blocker = $accessBlocker->block_access($accesCondition, $messageNoAccess)){
        //     return $blocker;
        // }

        // Utilisation du voter CategoryVoter
        //-------------------------------------------------------------------------------------------------------------------
        //$this->denyAccessUnlessGranted("CAN_EDIT", $category, "Vous ne pouvez pas modifier cette catégorie");


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
