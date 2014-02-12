<?php

namespace Bigfoot\Bundle\NavigationBundle\DataFixtures\ORM;

use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;

use Bigfoot\Bundle\NavigationBundle\Entity\Menu\Item\Attribute as Attribute;

class LoadMenuItemAttributeData extends AbstractFixture implements ContainerAwareInterface
{
    public $container;

    public function setContainer(ContainerInterface $container = null)
    {
        $this->container = $container;
    }

    /**
     * Load data fixtures with the passed EntityManager
     *
     * @param ObjectManager $manager
     */
    public function load(ObjectManager $manager)
    {
        $attribute = new Attribute();
        $attribute
            ->setType(Attribute::ELEMENT)
            ->setName('class')
            ->setLabel('Active element')
            ->setValue('active');

        $repository = $manager->getRepository('Gedmo\Translatable\Entity\Translation');
        $repository->translate($attribute, 'label', 'fr', 'Element ayant la class "active"');

        $manager->persist($attribute);

        $attribute = new Attribute();
        $attribute
            ->setType(Attribute::LINK)
            ->setName('class')
            ->setLabel('Active link')
            ->setValue('active');

        $repository = $manager->getRepository('Gedmo\Translatable\Entity\Translation');
        $repository->translate($attribute, 'label', 'fr', 'Lien ayant la class "active"');

        $manager->persist($attribute);

        $attribute = new Attribute();
        $attribute
            ->setType(Attribute::CHILD)
            ->setName('class')
            ->setLabel('Active child')
            ->setValue('active');

        $repository = $manager->getRepository('Gedmo\Translatable\Entity\Translation');
        $repository->translate($attribute, 'label', 'fr', 'Enfant ayant la class "active"');

        $manager->persist($attribute);

        $manager->flush();
    }
}