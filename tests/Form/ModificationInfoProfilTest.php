<?php

namespace App\Tests\Form;

use App\Entity\Participant;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Form\Test\TypeTestCase;

class ModificationInfoProfilTest extends TypeTestCase
{

    protected $participant;

    protected function setUp(): void
    {
        $this->participant = new Participant();
        $this->participant->setNom('Nom initial');
        $this->participant->setPrenom('Prénom initial');
    }

    public function testModificationInformationProfil()
    {
        // Simuler l'action de modification
        $this->participant->setNom('Nouveau nom');
        $this->participant->setPrenom('Nouveau prénom');

        // Vérifier le résultat
        $this->assertEquals('Nouveau nom', $this->participant->getNom());
        $this->assertEquals('Nouveau prénom', $this->participant->getPrenom());
    }

    protected function tearDown(): void
    {
        $this->participant = null;
    }
}