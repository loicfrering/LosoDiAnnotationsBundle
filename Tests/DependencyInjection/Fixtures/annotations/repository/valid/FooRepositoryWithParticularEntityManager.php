<?php
use Doctrine\ORM\EntityRepository;

/** @Repository(name="test.foo.repository", entity="FooEntity", entityManager="test") */
class FooRepositoryWithParticularEntityManager extends EntityRepository
{

}

