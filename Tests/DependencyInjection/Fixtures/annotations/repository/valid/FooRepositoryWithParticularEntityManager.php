<?php
use Doctrine\ORM\EntityRepository;
use LoSo\LosoBundle\DependencyInjection\Annotations\Repository;

/** @Repository(name="test.foo.repository", entity="FooEntity", entityManager="test") */
class FooRepositoryWithParticularEntityManager extends EntityRepository
{

}

