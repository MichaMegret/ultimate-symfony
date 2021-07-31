<?php

namespace App\DataFixtures;

use Faker\Factory;
use App\Entity\Product;
use Liior\Faker\Prices;
use App\Entity\Category;
use App\Entity\Purchase;
use App\Entity\PurchaseItem;
use App\Entity\User;
use Cocur\Slugify\Slugify;
use Mmo\Faker\PicsumProvider;
use Bezhanov\Faker\Provider\Commerce;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\String\Slugger\SluggerInterface;

class AppFixtures extends Fixture
{
    protected $slugger;
    protected $encoder;

    public function __construct(SluggerInterface $slugger, UserPasswordHasherInterface $encoder){
        $this->slugger = $slugger;
        $this->encoder=$encoder;
    }

    public function load(ObjectManager $manager)
    {
        $faker = Factory::create("fr_FR");
        $faker->addProvider(new Prices($faker));
        $faker->addProvider(new Commerce($faker));
        $faker->addProvider(new PicsumProvider($faker));
        $slugify = new Slugify();
        
        $users = [];

        $admin = new User;

        $hash = $this->encoder->hashPassword($admin, "password");

        $admin->setEmail("admin@gmail.com")
            ->setPassword($hash)
            ->setFullName("Admin")
            ->setRoles(["ROLE_ADMIN"]);

        $manager->persist($admin);

        $users[] = $admin;

        for($i=0; $i<5; $i++){
            $user = new User;

            $hash = $this->encoder->hashPassword($user, "password");

            $user->setEmail("user$i@gmail.com")
                ->setFullName($faker->name())
                ->setPassword($hash);

            $users[] = $user;

            $manager->persist($user);
        }

        $products = [];

        for($j=0; $j<3; $j++){
            $category = new Category;
            $category->setName($faker->department)
                ->setEditor($faker->randomElement($users));
            $manager->persist($category);

            for($i=0; $i < mt_rand(15, 20);$i++){
                $url = $faker->imageUrl(400, 400,null,true);                
                $product = new Product;
                $product
                    ->setName($faker->productName)
                    ->setPrice(mt_rand(1500, 20000) / 100)
                    // ->setSlug(strtolower($slugify->slugify($product->getName()))) => Mise en place depuis event listener doctrine
                    ->setCategory($category)
                    ->setShortDescription($faker->paragraph())
                    ->setMainPicture($url);
                $products[] = $product;
                $manager->persist($product);
            }
        }

        for($i=0; $i<mt_rand(20, 40);$i++){
            $purchase = new Purchase;

            $purchase->setFullName($faker->name)
                ->setAdress($faker->streetAddress)
                ->setPostalCode($faker->postcode)
                ->setCity($faker->city)
                ->setUser($faker->randomElement($users))
                ->setTotal(mt_rand(2000, 30000) / 100)
                ->setPurchasedAt($faker->dateTimeBetween("-6 months"));

            $selectedProducts=$faker->randomElements($products, mt_rand(2, 5));

            foreach($selectedProducts as $product){
                $purchaseItem = new PurchaseItem;
                $purchaseItem->setProduct($product)
                    ->setProductName($product->getName())
                    ->setProductPrice($product->getPrice())
                    ->setPurchase($purchase)
                    ->setQuantity(mt_rand(1,3))
                    ->setTotal($purchaseItem->getQuantity() * $purchaseItem->getProductPrice());
                $manager->persist($purchaseItem);
            }

            if($faker->boolean(80)){
                $purchase->setStatut($purchase::STATUT_PAID);
            }

            $manager->persist($purchase);
        }

        $manager->flush();
    }
}
