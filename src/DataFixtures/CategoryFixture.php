<?php

namespace App\DataFixtures;

use App\Entity\Category;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class CategoryFixture extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $categories = [
            ['Électronique', 'Appareils électroniques et gadgets'],
            ['Vêtements', 'Vêtements pour hommes, femmes et enfants'],
            ['Alimentation', 'Produits alimentaires et boissons'],
            ['Meubles', 'Meubles pour la maison et le bureau'],
            ['Jouets', 'Jouets pour enfants et jeux']
        ];

        foreach ($categories as [$name, $description]) {
            $category = new Category();
            $category->setTitre($name);
            $category->setDescription($description);
            $manager->persist($category);
        }

        $manager->flush();
    }
}
