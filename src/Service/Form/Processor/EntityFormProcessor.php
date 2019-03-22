<?php

namespace App\Service\Form\Processor;

use App\Entity\EntityInterface;
use App\Service\Mutator\MutatorInterface;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormTypeInterface;
use Symfony\Component\HttpFoundation\Request;

/**
 * Class EntityFormProcessor
 *
 * @package      App\Service\Form\Processor
 * @author       Vladimir Strackovski <vladimir.strackovski@gmail.com>
 * @copyright    2019 Vladimir Strackovski
 */
class EntityFormProcessor
{
    /**
     * @var FormFactoryInterface
     */
    protected $formFactory;

    /**
     * @var MutatorInterface
     */
    private $mutator;

    /**
     * EntityFormProcessor constructor.
     *
     * @param FormFactoryInterface $formFactory
     * @param MutatorInterface     $mutator
     */
    public function __construct(FormFactoryInterface $formFactory, MutatorInterface $mutator)
    {
        $this->formFactory = $formFactory;
        $this->mutator = $mutator;
    }

    /**
     *
     *
     * @param EntityInterface        $entity
     * @param Request                $request
     * @param null|FormTypeInterface $type
     * @param array                  $options
     * @param bool                   $returnForm
     *
     * @return EntityInterface|FormInterface
     * @throws \Exception
     */
    public function process(EntityInterface $entity, Request $request, ?FormTypeInterface $type = null, array $options = [], $returnForm = false)
    {
        $form = $this->createForm(
            $type ? $type : $this->getFormType($entity),
            $entity,
            $options
        );

//        $data = $request->request->get($form->getName());

        $data = json_decode($request->getContent(), true);

//        dump($form->getName(), $data);die;

        $form = $form->submit($data, false);

        if ($form->isSubmitted() && $form->isValid()) {
            $object = $this->mutator->save($form->getData());
            return $returnForm ? $form : $object;
        }

        return $form;
    }

    /**
     * @param EntityInterface $entity
     *
     * @return string
     * @throws \Exception
     */
    protected function getFormType(EntityInterface $entity)
    {
        $reflection = new \ReflectionObject($entity);
        $typeClass = sprintf("App\\Form\\%sType", $reflection->getShortName());

        if (!class_exists($typeClass)) {
            throw new \Exception(sprintf('Could not load type "%s": class does not exist.', $typeClass));
        }

        return $typeClass;
    }

    /**
     * Creates and returns a Form instance from the type of the form.
     *
     * @final
     *
     * @param string $type
     * @param null   $data
     * @param array  $options
     *
     * @return FormInterface
     */
    protected function createForm(string $type, $data = null, array $options = array()): FormInterface
    {
        return $this->formFactory->create($type, $data, $options);
    }
}
