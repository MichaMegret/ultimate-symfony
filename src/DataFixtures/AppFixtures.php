<?php

namespace App\DataFixtures;

use App\Entity\Category;
use Faker\Factory;
use App\Entity\Product;
use Liior\Faker\Prices;
use Bezhanov\Faker\Provider\Commerce;
use Cocur\Slugify\Slugify;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Symfony\Component\String\Slugger\SluggerInterface;

class AppFixtures extends Fixture
{
    protected $slugger;

    public function __construct(SluggerInterface $slugger){
        $this->slugger = $slugger;
    }

    public function load(ObjectManager $manager)
    {
        $faker = Factory::create("fr_FR");
        $faker->addProvider(new Prices($faker));
        $faker->addProvider(new Commerce($faker));
        $slugify = new Slugify();

        for($j=0; $j<3; $j++){
            $category = new Category;
            $category->setName($faker->department)
                ->setSlug(strtolower($slugify->slugify($category->getName())));
            $manager->persist($category);

            for($i=0; $i < mt_rand(15, 20);$i++){
                $product = new Product;
                $product
                    ->setName($faker->productName)
                    ->setPrice($faker->price(1500, 20000))
                    ->setSlug(strtolower($slugify->slugify($product->getName())))
                    ->setCategory($category);
                $manager->persist($product);
            }
        }

        $manager->flush();
    }
}
