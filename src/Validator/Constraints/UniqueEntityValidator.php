<?php
/**
 * Author: Mykola Chomenko
 * Email: mykola.chomenko@dipcom.cz
 */

namespace Chomenko\ExtraForm\Validator\Constraints;

use Doctrine\ORM\Mapping\ClassMetadata;
use Doctrine\ORM\EntityManager;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\ConstraintViolationList;
use Symfony\Component\Validator\Exception\ConstraintDefinitionException;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;

class UniqueEntityValidator extends ConstraintValidator
{

	/**
	 * @var EntityManager
	 */
	private $em;

	/**
	 * @param EntityManager $em
	 */
	public function __construct(EntityManager $em)
	{
		$this->em = $em;
	}

	/**
	 * @param object $entity
	 * @param Constraint $constraint
	 * @return ConstraintViolationList|void
	 */
	public function validate($entity, Constraint $constraint)
	{
		if (!$constraint instanceof UniqueEntity) {
			throw new UnexpectedTypeException($constraint, __NAMESPACE__ . '\UniqueEntity');
		}

		if (!is_array($constraint->fields) && !is_string($constraint->fields)) {
			throw new UnexpectedTypeException($constraint->fields, 'array');
		}

		$fields = (array)$constraint->fields;

		if (count($fields) === 0) {
			throw new ConstraintDefinitionException('At least one field has to be specified.');
		}

		if ($entity === NULL) {
			return;
		}

		/** @var ClassMetadata $class */
		$class = $this->em->getClassMetadata(get_class($entity));

		if (empty($class->getIdentifier())) {
			throw new ConstraintDefinitionException(sprintf('The entity "%s" is not define identifier.', $entity));
		}

		$qb = $this->em->getRepository(get_class($entity))->createQueryBuilder("probe");

		$identifiers = $class->getIdentifier();
		$identifierName = end($identifiers);

		$identifierValue = $class->getFieldValue($entity, $identifierName);

		if ($identifierValue !== NULL) {
			$qb->andWhere("probe.{$identifierName} != :{$identifierName}");
			$qb->setParameter($identifierName, $identifierValue);
		}

		foreach ($fields as $fieldName) {
			if (!$class->hasField($fieldName) && !$class->hasAssociation($fieldName)) {
				throw new ConstraintDefinitionException(sprintf('The field "%s" is not mapped by Doctrine, so it cannot be validated for uniqueness.', $fieldName));
			}
			$value = $class->getFieldValue($entity, $fieldName);
			$class->getFieldValue($entity, $fieldName);
			$qb->andWhere("probe." . $fieldName . " = :" . $fieldName);
			$qb->setParameter($fieldName, $value);
		}

		$result = $qb->getQuery()->getArrayResult();
		if (count($result) > 0) {
			return $constraint->message;
		}
		return;
	}

}
