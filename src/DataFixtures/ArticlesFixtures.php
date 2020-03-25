<?php

namespace App\DataFixtures;

use App\Entity\Articles;
use App\Entity\Category;
use App\Entity\Comment;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;

class ArticlesFixtures extends Fixture
{
    public function load(ObjectManager $manager)
    {
        $faker = \Faker\Factory::create('fr_FR');

        //créer 3 cat faker:
        for ($i = 1; $i <= 3; $i++) {
            $category = new Category();
            $category->setTitle($faker->sentence())
                ->setDescription($faker->paragraph());
            $manager->persist($category);
        }


        // Créer entre 4 et 6 articles:
        for ($j = 1; $j <= mt_rand(4, 6); $j++) {
            $article = new Articles;
            $content = '<p>' . join($faker->paragraphs(5), '</p><p>');
            $article->setTitle($faker->sentence())
                ->setContent($content)
                ->setImg($faker->imageUrl())
                ->setCreatedAt($faker->dateTimeBetween('-6 months'))
                ->setCategory($category);
            $manager->persist($article);
        }

        // On donne des commentaires:
        for ($j = 1; $j <= mt_rand(4, 6); $j++) {
            $comment = new Comment;
            $content = '<p>' . join($faker->paragraphs(2), '</p><p>');

            $now= new \DateTime();
            $interval=$now->diff($article->getCreatedAt());
            $days= $interval->days;
            $minimum = '-'.$days.' days';
            $comment->setAuthor($faker->name)
                ->setContent($content)
                ->setCreatedAt($faker->dateTimeBetween($minimum))
                ->setArticle($article);
            $manager->persist($comment);
        }




        $manager->flush();
    }
}
