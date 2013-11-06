<?php

namespace Bigfoot\Bundle\NavigationBundle\Form\DataTransformer;

use Bigfoot\Bundle\NavigationBundle\Entity\Item;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\EntityManager;
use Symfony\Component\Form\DataTransformerInterface;

class ItemsToJsonTransformer implements DataTransformerInterface
{
    /**
     * @var \Doctrine\ORM\EntityManager
     */
    protected $entityManager;

    /**
     * Constructor.
     *
     * @param EntityManager $entityManager
     */
    public function __construct(EntityManager $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    /**
     * Transforms a value from the original representation to a transformed representation.
     *
     * This method is called on two occasions inside a form field:
     *
     * 1. When the form field is initialized with the data attached from the datasource (object or array).
     * 2. When data from a request is submitted using {@link Form::submit()} to transform the new input data
     *    back into the renderable format. For example if you have a date field and submit '2009-10-10'
     *    you might accept this value because its easily parsed, but the transformer still writes back
     *    "2009/10/10" onto the form field (for further displaying or other purposes).
     *
     * This method must be able to deal with empty values. Usually this will
     * be NULL, but depending on your implementation other empty values are
     * possible as well (such as empty strings). The reasoning behind this is
     * that value transformers must be chainable. If the transform() method
     * of the first value transformer outputs NULL, the second value transformer
     * must be able to process that value.
     *
     * By convention, transform() should return an empty string if NULL is
     * passed.
     *
     * @param mixed $value The value in the original representation
     *
     * @return mixed The value in the transformed representation
     *
     * @throws TransformationFailedException When the transformation fails.
     */
    public function transform($items)
    {
        if (null === $items) {
            return "";
        }

        $toJson = $this->recursiveTransform($items, true);

        return json_encode($toJson);
    }

    /**
     * Transforms an ArrayCollection|array of Item objects into an array usable by the jQuery tree plugin.
     *
     * @param ArrayCollection|array $items
     * @return array
     */
    private function recursiveTransform($items, $firstLevel = false) {
        $itemsArray = array();
        foreach ($items as $item) {
            if (!$firstLevel or !$item->getParent()) {
                $itemArray = array(
                    'id' => $item->getId(),
                    'label' => $item->getName(),
                );
                if (count($children = $item->getChildren()) > 0) {
                    $itemArray['children'] = $this->recursiveTransform($children);
                }
                $itemsArray[$item->getPosition()] = $itemArray;
            }
        }

        ksort($itemsArray);
        return $itemsArray;
    }

    /**
     * Transforms a value from the transformed representation to its original
     * representation.
     *
     * This method is called when {@link Form::submit()} is called to transform the requests tainted data
     * into an acceptable format for your data processing/model layer.
     *
     * This method must be able to deal with empty values. Usually this will
     * be an empty string, but depending on your implementation other empty
     * values are possible as well (such as empty strings). The reasoning behind
     * this is that value transformers must be chainable. If the
     * reverseTransform() method of the first value transformer outputs an
     * empty string, the second value transformer must be able to process that
     * value.
     *
     * By convention, reverseTransform() should return NULL if an empty string
     * is passed.
     *
     * @param mixed $value The value in the transformed representation
     *
     * @return mixed The value in the original representation
     *
     * @throws TransformationFailedException When the transformation fails.
     */
    public function reverseTransform($json)
    {
        if (!$json) {
            return null;
        }

        $toReturn = new ArrayCollection();

        $this->recursiveReverseTransform(json_decode($json), $toReturn);

        return $toReturn;
    }

    public function recursiveReverseTransform($items, $collection, $parent = null)
    {
        $repository = $this->entityManager->getRepository('BigfootNavigationBundle:Item');
        $i = 1;
        foreach ($items as $item) {
            $toAdd = $repository->findOneById($item->id);
            if (array_key_exists('children', $item)) {
                $this->recursiveReverseTransform($item->children, $collection, $toAdd);
            }
            $toAdd->setParent($parent);
            $toAdd->setPosition($i);
            $collection->add($toAdd);
            $i++;
        }
    }
}
