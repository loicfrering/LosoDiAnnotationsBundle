Repository definition
=====================

You can easily declare custom entity repositories in the service container
thanks to the `@Repository` annotation. You just need to specifiy on which
entity the repository will act for.

Usage::

    @Repository("FooBundle:BarEntity")
    @Repository("My\FooBundle\Entity\BarEntity")

    @Repository(name="foo.repository", entity="FooBundle:BarEntity")

    @Repository(entity="FooBundle:BarEntity", entityManager="custom")

Example::

    use Loso\Bundle\DiAnnotationsBundle\DependencyInjection\Annotations as DI;
    use Doctrine\ORM\EntityRepository;

    /** @DI\Repository("FooBundle:Item") */
    class ItemRepository extends EntityRepository
    {
        public function findByCategory($category)
        {
            $q = $this->createQueryBuilder('i')
                      ->where(':category MEMBER OF i.categories')
                      ->getQuery();

            return $q->execute(array('category' => $category));
        }
    }

Now you can easily inject the repository in your controller::

    use Loso\Bundle\DiAnnotationsBundle\DependencyInjection\Annotations as DI;

    /** @DI\Controller */
    class ItemController
    {
        /** @DI\Inject **/
        public function __construct($itemRepository)
        {
            $this->itemRepository = $itemRepository;
        }

        // ....
    }
