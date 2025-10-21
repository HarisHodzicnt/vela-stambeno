<?php

namespace App\DataFixtures;

use App\Entity\Property;
use App\Entity\PropertyImage;
use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;

class PropertyFixtures extends Fixture implements DependentFixtureInterface
{
    public function load(ObjectManager $manager): void
    {
        $owner = $manager->getRepository(User::class)->findOneBy(['email' => 'owner@stambeno.ba']);

        if (!$owner) {
            // If the owner user doesn't exist, you might want to create it
            // or throw an exception. For this fixture, we'll assume the UserFixtures ran.
            return;
        }

        $property = new Property();
        $property->setOwner($owner);
        $property->setTitle('Cijeli apartman u vlasništvu: Haris');
        $property->setDescription('Ovaj prelijepi apartman se nalazi u samom srcu Sarajeva, na samo nekoliko koraka od Baščaršije. Potpuno renoviran i moderno opremljen, nudi savršen spoj udobnosti i stila. Uživajte u pogledu na grad sa prostranog balkona. Idealan za parove, solo avanturiste i poslovne putnike.');
        $property->setPropertyType('apartment');
        $property->setListingType('rent');
        $property->setAddressLine1('Aščiluk 23');
        $property->setCity('Sarajevo');
        $property->setMunicipality('Stari Grad');
        $property->setCountry('BA');
        $property->setBedrooms(2);
        $property->setBathrooms('1');
        $property->setSizeSqm('55');
        $property->setPricePerNight('150.00');
        $property->setStatus('active');
        $property->setCoverImageUrl('https://images.unsplash.com/photo-1512917774080-9991f1c4c750?w=800');
        
        $manager->persist($property);
        
        // Add property images
        $imageUrls = [
            'https://images.unsplash.com/photo-1512917774080-9991f1c4c750?w=800',
            'https://images.unsplash.com/photo-1522708323590-d24dbb6b0267?w=800',
            'https://images.unsplash.com/photo-1560448204-e02f11c3d0e2?w=800',
            'https://images.unsplash.com/photo-1502672260266-1c1ef2d93688?w=800',
            'https://images.unsplash.com/photo-1600596542815-ffad4c1539a9?w=800'
        ];
        
        foreach ($imageUrls as $index => $url) {
            $image = new PropertyImage();
            $image->setProperty($property);
            $image->setUrl($url);
            $image->setDisplayOrder($index);
            $manager->persist($image);
        }
        
        $manager->flush();
    }

    public function getDependencies()
    {
        return [
            UserFixtures::class,
        ];
    }
}
