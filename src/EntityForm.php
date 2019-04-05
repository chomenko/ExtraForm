<?php
/**
 * Author: Mykola Chomenko
 * Email: mykola.chomenko@dipcom.cz
 */

namespace Chomenko\ExtraForm;

use Chomenko\ExtraForm\Controls\ControlEvents;
use Chomenko\ExtraForm\Controls\FormElement;
use Chomenko\ExtraForm\Controls\Traits\Extend;
use Chomenko\ExtraForm\Extend\ExtendValue;
use Chomenko\ExtraForm\Extend\IEntityExtend;
use Chomenko\ExtraForm\Extend\Pair\Pairs;
use Chomenko\ExtraForm\Exception\Exception;
use Doctrine\Common\Annotations\AnnotationReader;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Mapping\ClassMetadata;
use Doctrine\ORM\UnitOfWork;
use Nette\ComponentModel\IComponent;
use Nette\DI\Container;
use Nette\Forms\Controls\BaseControl;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidatorInterface;
use Symfony\Component\Validator\ConstraintViolationList;
use Symfony\Component\Validator\Validator\RecursiveValidator;

class EntityForm extends ExtraForm
{

	const INSTALL_ENTITY = "installEntity";

	/**
	 * @var object
	 */
	private $entity;

	/**
	 * @var EntityManager
	 */
	private $entityManager;

	/**
	 * @var UnitOfWork
	 */
	private $unitOfWork;

	/**
	 * @var ClassMetadata
	 */
	private $metaData;

	/**
	 * @var array
	 */
	private $originalEntityData = [];

	/**
	 * @var string
	 */
	private $identifier;

	/**
	 * @var bool
	 */
	private $dataSet = FALSE;

	/**
	 * @var AnnotationReader
	 */
	private $annotationReader;

	/**
	 * @var Container
	 */
	private $container;

	/**
	 * @var Constraint[]
	 */
	protected $constraint = [];

	/**
	 * @param string|object $entity
	 * @param Container $container
	 * @param EntityManager $entityManager
	 * @param FormEvents $formEvents
	 * @throws Exception
	 * @throws \Doctrine\Common\Annotations\AnnotationException
	 */
	public function __construct($entity, Container $container, EntityManager $entityManager, FormEvents $formEvents = NULL)
	{
		$this->entityManager = $entityManager;
		$this->unitOfWork = $entityManager->getUnitOfWork();
		parent::__construct(NULL, NULL, $formEvents);
		$this->annotationReader = new AnnotationReader();
		$this->identifier = $this->installEntity($entity);
		$this->container = $container;
	}

	/**
	 * @param \Nette\ComponentModel\IComponent $presenter
	 */
	public function attached($presenter)
	{
		parent::attached($presenter);
		/** @var BaseControl $component */
		foreach ($this->components as $component) {
			$name = $component->getName();
			if (!$this->hasEntityField($name)) {
				continue;
			}

			if (method_exists($component, 'isUsedDefaultValue') && !$component->isUsedDefaultValue()) {
				$value = $this->getFieldValue($name);
				$component->setDefaultValue($value);
			}
		}
	}

	/**
	 * @param IComponent $component
	 * @param int|null|string $name
	 * @param null $insertBefore
	 * @return static
	 * @throws \ReflectionException
	 */
	public function addComponent(IComponent $component, $name, $insertBefore = NULL)
	{
		if ($component instanceof FormElement) {
			$events = $component->getListener();
			foreach ($component->getOptions() as $key => $value) {
				if ($value instanceof IEntityExtend) {
					$value->attached($component, $this);
				}
			}
			$events->create(ControlEvents::SET_OPTION, function (BaseControl $control, $key, $value) {
				if ($value instanceof IEntityExtend) {
					$value->attached($control, $this);
				}
			});
		}

		$container = parent::addComponent($component, $name, $insertBefore);
		if ($component instanceof FormElement) {
			$this->applyAsserts($component, $name);
		}
		return $container;
	}

	/**
	 * @param string|object $entity
	 * @return string
	 * @throws Exception
	 */
	private function installEntity($entity): string
	{
		$meta = NULL;
		if (is_string($entity)) {
			$entity = $this->createEntityInstance($entity);
		}

		if (!is_object($entity)) {
			throw Exception::typeStringOrObject($entity);
		}
		$this->entity = $entity;
		$this->metaData = $meta ? $meta : $this->entityManager->getClassMetadata(get_class($entity));

		if ($entity instanceof \Doctrine\Common\Persistence\Proxy && !$entity->__isInitialized()) {
			$entity->__load();
		}

		if ($this->entityManager->contains($entity)) {
			$this->originalEntityData = $data = $this->unitOfWork->getOriginalEntityData($entity);
		} else {
			foreach ($this->metaData->getFieldNames() as $name) {
				$this->originalEntityData[$name] = $this->metaData->getFieldValue($entity, $name);
			}
		}

		$refClass = new \ReflectionClass($entity);
		$annotations = $this->annotationReader->getClassAnnotations($refClass);
		foreach ($annotations as $annotation) {
			if ($annotation instanceof Constraint) {
				$this->constraint[] = $annotation;
			}
		}

		$this->eventsListener->emit(self::INSTALL_ENTITY, $entity, $this);
		return $this->getEntityIdentifier($entity);
	}

	/**
	 * @param IComponent|Extend $component
	 * @param string $name
	 * @throws \ReflectionException
	 */
	protected function applyAsserts(IComponent $component, string $name)
	{
		if ($this->hasEntityField($name) && $component instanceof BaseControl) {
			$property = new \ReflectionProperty($this->metaData->getName(), $name);
			$annotations = $this->annotationReader->getPropertyAnnotations($property);
			if ($this->metaData->hasField($name)) {
				$component->setRequired(!$this->metaData->isNullable($name));
			}
			foreach ($annotations as $annotation) {
				if ($annotation instanceof Constraint) {
					$component->addConstraint($annotation);
				}
			}
		}
	}

	/**
	 * @param object $entity
	 * @return string
	 */
	protected function getEntityIdentifier(object $entity)
	{
		$metadata = $this->entityManager->getClassMetadata(get_class($entity));
		$identifier = $metadata->getIdentifier();
		return $identifier[0];
	}

	/**
	 * @param string $name
	 * @return bool
	 */
	public function hasEntityField(string $name): bool
	{
		return $this->metaData->hasField($name) || $this->metaData->hasAssociation($name);
	}

	/**
	 * @param string $name
	 * @return mixed
	 */
	public function getFieldValue(string $name)
	{
		if (array_key_exists($name, $this->originalEntityData)) {
			return $this->originalEntityData[$name];
		}
	}

	/**
	 * @return object
	 * @throws \Exception
	 */
	public function getData()
	{
		if (!$this->isSubmitted() || $this->dataSet) {
			return $this->entity;
		}
		$this->setEntityValues($this->entity);
		$this->dataSet = TRUE;
		return $this->entity;
	}

	/**
	 * @param object $entity
	 * @param bool $events
	 */
	protected function setEntityValues(object $entity, bool $events = TRUE)
	{
		/** @var BaseControl $component */
		foreach ($this->components as $component) {
			$name = $component->getName();
			if (!$this->hasEntityField($name)) {
				continue;
			}
			$value = $component->getValue();
			if ($events) {
				$extendValue = new ExtendValue($component->getValue());
				foreach ($component->getOptions() as $option) {
					if ($option instanceof IEntityExtend) {
						$option->executeData($entity, $extendValue);
					}
				}
				$value = $extendValue->getNewValue();
			}
			$this->metaData->setFieldValue($entity, $name, $value);
		}
	}

	/**
	 * @param string $entity
	 * @return object
	 * @internal
	 */
	public function createEntityInstance(string $entity): object
	{
		$meta = $this->entityManager->getClassMetadata($entity);
		$method = $meta->getReflectionClass()->getConstructor();
		$withoutConstructor = FALSE;
		if (!empty($method)) {
			foreach ($method->getParameters() as $parameter) {
				if (!$parameter->isOptional()) {
					$withoutConstructor = TRUE;
					break;
				}
			}
		}

		if ($withoutConstructor) {
			$entity = $meta->newInstance();
		} else {
			$entity = new $entity();
		}
		return $entity;
	}

	/**
	 * @param string $entity
	 * @param string $pattern
	 * @param string $identifier
	 * @return Pairs
	 */
	public static function createPairs(string $entity, string $pattern = NULL, string $identifier = NULL)
	{
		$pairs = new Pairs($entity, $pattern);
		if ($identifier) {
			$pairs->seIdentifier($identifier);
		}
		return $pairs;
	}

	/**
	 * @return EntityManager
	 */
	public function getEntityManager(): EntityManager
	{
		return $this->entityManager;
	}

	/**
	 * @return UnitOfWork
	 */
	public function getUnitOfWork(): UnitOfWork
	{
		return $this->unitOfWork;
	}

	/**
	 * @return object
	 */
	public function getEntity(): object
	{
		return $this->entity;
	}

	/**
	 * @return ClassMetadata
	 */
	public function getMetaData(): ClassMetadata
	{
		return $this->metaData;
	}

	/**
	 * @return array
	 */
	public function getOriginalEntityData(): array
	{
		return $this->originalEntityData;
	}

	public function validate(array $controls = NULL)
	{
		parent::validate($controls);

		if (!empty($this->constraint)) {

			$entity = clone $this->entity;

			$this->setEntityValues($entity, FALSE);

			foreach ($this->constraint as $constraint) {
				$validator = $this->getValidatorByConstraint($constraint);
				$error = $validator->validate($entity, $constraint);
				if ($error instanceof ConstraintViolationList) {
					$this->addConstraintErrors($error);
				}
				if (is_string($error)) {
					$message = $this->translatorWrapped->translate($error);
					$this->addError($message);
				}
			}
			unset($entity);
		}
	}

	/**
	 * @param Constraint $constraint
	 * @return ConstraintValidatorInterface|RecursiveValidator
	 * @throws Exception
	 */
	public function getValidatorByConstraint(Constraint $constraint)
	{
		$validator = $this->getValidator();
		if (method_exists($constraint, "validatedBy")) {
			$tag = $constraint->validatedBy();
			if (class_exists($tag)) {
				return $validator;
			}
			$validator = $this->getValidateService($tag);
		}
		return $validator;
	}

	/**
	 * @param string $name
	 * @return ConstraintValidatorInterface
	 * @throws Exception
	 */
	public function getValidateService(string $name): ConstraintValidatorInterface
	{
		$validators = $this->container->findByTag($name);
		reset($validators);
		$serviceName = key($validators);

		if (empty($validators)) {
			throw Exception::constraintServiceUnregistered($name);
		}

		$validator = $this->container->getService($serviceName);
		if (!$validator instanceof ConstraintValidatorInterface) {
			throw Exception::constraintServiceValidatorMustInstance($validator);
		}
		return $validator;
	}

}
