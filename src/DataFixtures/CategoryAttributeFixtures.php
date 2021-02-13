<?php

namespace App\DataFixtures;

use App\Entity\Category;
use App\Entity\CategoryAttribute;
use App\Repository\RessourceRepository;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;

class CategoryAttributeFixtures extends Fixture implements DependentFixtureInterface
{


    public function load(ObjectManager $manager)
    {

        /** @var Category $category */
        $category1=$this->getReference('category1');
        $category2=$this->getReference('category2');
        $category3=$this->getReference('category3');

        //        Attributs de Bien
        $attribute1 = new CategoryAttribute();
        $attribute1->setName('Taille');
        $attribute1->setUnity('m');
        $attribute1->addCategory($category1);

        $attribute2 = new CategoryAttribute();
        $attribute2->setName('Poids');
        $attribute2->setUnity('kg');
        $attribute2->addCategory($category1);

        //        Attributs de Service
        $attribute3 = new CategoryAttribute();
        $attribute3->setName('Temps');
        $attribute3->setUnity('h');
        $attribute3->addCategory($category2);


        //        Attributs de Véhicule
        $attribute4 = new CategoryAttribute();
        $attribute4->setName('Marque');
        $attribute4->addCategory($category3);

        $attribute5 = new CategoryAttribute();
        $attribute5->setName('Cylindrée');
        $attribute5->setUnity('cc');
        $attribute5->addCategory($category3);

        //        ---------

        $manager->persist($attribute1);
        $manager->persist($attribute2);
        $manager->persist($attribute3);
        $manager->persist($attribute4);
        $manager->persist($attribute5);

        $manager->flush();
    }

    public function getDependencies()
    {
        return array(
            CategoryFixtures::class,
        );
    }

}
