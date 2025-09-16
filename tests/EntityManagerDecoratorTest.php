<?php

declare(strict_types=1);

namespace Tests;

use App\Doctrine\DecoratedEntityManager;
use App\Entity\Book;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bridge\Doctrine\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

final class EntityManagerDecoratorTest extends KernelTestCase
{
    public function testEntityManagerReset(): void
    {
        // Arrange
        $book = new Book('Symfony 6: The fast track');
        /** @var ManagerRegistry $doctrine */
        $doctrine = self::getContainer()->get('doctrine');
        /** @var EntityManagerInterface $em */
        $em = $doctrine->getManagerForClass(Book::class);
        self::assertInstanceOf(DecoratedEntityManager::class, $em);

        $em->persist($book);
        $em->flush();
        $em->close();

        // Act
        $doctrine->resetManager();
        $book = $em->find(Book::class, $book->getId());
        $book->title = 'Symfony 7: The fast track';
        $em->flush();
        $em->refresh($book);

        // Assert
        self::assertSame('Symfony 7: The fast track', $book->title);
    }
}
