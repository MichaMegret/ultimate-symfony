<?php

namespace App\DataFixtures;

use Faker\Factory;
use App\Entity\Product;
use Liior\Faker\Prices;
use App\Entity\Category;
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

        $admin = new User;

        $hash = $this->encoder->hashPassword($admin, "password");

        $admin->setEmail("admin@gmail.com")
            ->setPassword($hash)
            ->setFullName("Admin")
            ->setRoles(["ROLE_ADMIN"]);

        $manager->persist($admin);

        for($i=0; $i<5; $i++){
            $user = new User;

            $hash = $this->encoder->hashPassword($user, "password");

            $user->setEmail("user$i@gmail.com")
                ->setFullName($faker->name())
                ->setPassword($hash);
            $manager->persist($user);
        }

        for($j=0; $j<3; $j++){
            $category = new Category;
            $category->setName($faker->department)
                ->setSlug(strtolower($slugify->slugify($category->getName())));
            $manager->persist($category);

            for($i=0; $i < mt_rand(15, 20);$i++){
                $url = $faker->imageUrl(400, 400,null,true);                
                $product = new Product;
                $product
                    ->setName($faker->productName)
                    ->setPrice($faker->price(1500, 20000))
                    ->setSlug(strtolower($slugify->slugify($product->getName())))
                    ->setCategory($category)
                    ->setShortDescription($faker->paragraph())
                    ->setMainPicture($url);
                $manager->persist($product);
            }
        }

        $manager->flush();
    }
}
