<?php
use Doctrine\ORM\EntityRepository;

/** @Repository(entity="FooEntity", entityManager="test") */
class FooRepositoryWithParticularEntityManager extends EntityRepository
{

}

