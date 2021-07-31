<?php 

namespace App\Controller\Purchase;

use App\Entity\User;
use Twig\Environment;
use App\AccessManager\AccessBlocker;
use App\Repository\PurchaseRepository;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

class PurchasesListController extends AbstractController{

    /**
     * @Route("/purchases", name="purchase_index")
     * IsGranted("ROLE_USER", message="Vous devez être connecté pour accéder à cette page")
     */
    public function index(AccessBlocker $accessBlocker, PurchaseRepository $purchaseRepository){

        /** @var User */
        $user = $this->getUser();
        // if(!$user){
        //     //$this->addFlash("danger", "Vous devez être connecté pour accéder à cette page");
        //     throw new AccessDeniedException("Vous devez être connecté pour accéder à cette page");
        // }
        
        //dd($purchaseRepository->findBy(["user"=> $user]));

        if($blocker = $accessBlocker->block_access(($user!==null))){
            return $blocker;
        }

        return $this->render("purchase/index.html.twig", [
            "purchases"=>$purchaseRepository->findBy(["user"=> $user], ["purchasedAt"=>"DESC"])
        ]);

    }

}