<?php
/**
 * Author: Mykola Chomenko
 * Email: mykola.chomenko@dipcom.cz
 */

namespace Chomenko\ExtraForm\Extend\Pair;

use Kdyby\Doctrine\EntityManager;
use Doctrine\ORM\Mapping\ClassMetadata;

class PairsRelation
{

	/**
	 * @var string
	 */
	private $field;

	/**
	 * @var EntityManager
	 */
	private $em;

	/**
	 * @var string
	 */
	private $entity;

	/**
	 * @var ClassMetadata
	 */
	private $entityMeta;

	/**
	 * @var array
	 */
	private $entityMapping;

	/**
	 * @var string|null
	 */
	private $relationEntity;

	/**
	 * @var ClassMetadata|null
	 */
	private $relationMeta;

	/**
	 * @var array|null
	 */
	private $relationMapping;

	/**
	 * @var string
	 */
	private $rightEntity;

	/**
	 * @var ClassMetadata
	 */
	private $rightMeta;

	/**
	 * @var array|null
	 */
	private $rightMapping;

	/**
	 * @var bool
	 */
	private $relation = FALSE;

	/**
	 * @param string $field
	 * @param string $entity
	 * @param string $rightEntity
	 * @param EntityManager $entityManager
	 */
	public function __construct(string $field, string $entity, string $rightEntity, EntityManager $entityManager)
	{
		$this->field = $field;
		$this->entity = $entity;
		$this->rightEntity = $rightEntity;
		$this->em = $entityManager;

		$this->entityMeta = $this->em->getClassMetadata($entity);
		$this->rightMeta = $this->em->getClassMetadata($rightEntity);


		if ($this->entityMeta->hasField($field) || $this->entityMeta->hasAssociation($field)) {
			$this->relation = !$this->entityMeta->hasField($field);
		}

		if ($this->relation) {
			$this->entityMapping = $this->entityMeta->getAssociationMapping($this->field);
		} else {
			$this->entityMapping = $this->entityMeta->getFieldMapping($this->field);
		}

		$this->detectRelation();
	}


	protected function detectRelation()
	{
		if (!isset($this->entityMapping["targetEntity"])){
			return;
		}

		if ($this->entityMapping["targetEntity"] === $this->rightEntity) {
			if(!$inversedBy = $this->entityMapping["inversedBy"]) {
				return;
			}
			$this->rightMapping = $this->rightMeta->getAssociationMapping($inversedBy);
			return;
		}

		$this->relationMeta = $this->em->getClassMetadata($this->entityMapping["targetEntity"]);

		foreach ($this->relationMeta->getAssociationMappings() as $relation) {
			if ($relation["targetEntity"] === $this->rightEntity) {
				$this->relationEntity = $relation["sourceEntity"];
				$this->relationMapping = $relation;
			}
		}
	}

	/**
	 * @return null|string
	 */
	public function getRightColumnName(): ?string
	{
		if ($this->hasMediator() && isset($this->relationMapping["sourceToTargetKeyColumns"])) {
			$source = $this->relationMapping["sourceToTargetKeyColumns"];
			return end($source);
		}
	}

	/**
	 * @return null|string
	 */
	public function getEntityColumnName(): ?string
	{
		if ($this->hasMediator()) {
			$mapping = $this->relationMeta->getAssociationMapping($this->getRelationFieldName());
			$source = $mapping["sourceToTargetKeyColumns"];
			return end($source);
		}
	}

	/**
	 * @return null|string
	 */
	public function getRightRelationColumnName(): ?string
	{
		if ($this->hasMediator() && isset($this->relationMapping["targetToSourceKeyColumns"])) {
			$source = $this->relationMapping["targetToSourceKeyColumns"];
			return end($source);
		}
	}

	/**
	 * @return null|string
	 */
	public function getEntityRelationColumnName(): ?string
	{
		if ($this->hasMediator()) {
			$mapping = $this->relationMeta->getAssociationMapping($this->getRelationFieldName());
			$source = $mapping["targetToSourceKeyColumns"];
			return end($source);
		}
	}

	/**
	 * @return null|string
	 */
	public function getRightReferencedColumnName(): ?string
	{
		return $this->getRightRelationColumnName();
	}

	/**
	 * @return null|string
	 */
	public function getRightRelationFieldName(): ?string
	{
		if ($this->hasMediator() && isset($this->relationMapping["fieldName"])) {
			return $this->relationMapping["fieldName"];
		}
	}

	/**
	 * @return null|string
	 */
	public function getRelationFieldName(): ?string
	{
		if ($this->hasMediator() && isset($this->entityMapping["mappedBy"])) {
			return $this->entityMapping["mappedBy"];
		}
	}


	/**
	 * @return bool
	 */
	public function hasMediator(): bool
	{
		return !empty($this->relationEntity);
	}

	/**
	 * @return bool
	 */
	public function hasRelation(): bool
	{
		return $this->relation;
	}

	/**
	 * @return string
	 */
	public function getField(): string
	{
		return $this->field;
	}

	/**
	 * @return EntityManager
	 */
	public function getEm(): EntityManager
	{
		return $this->em;
	}

	/**
	 * @return string
	 */
	public function getEntity(): string
	{
		return $this->entity;
	}

	/**
	 * @return ClassMetadata
	 */
	public function getEntityMeta(): ClassMetadata
	{
		return $this->entityMeta;
	}

	/**
	 * @return array
	 */
	public function getEntityMapping(): array
	{
		return $this->entityMapping;
	}

	/**
	 * @return null|string
	 */
	public function getRelationEntity(): ?string
	{
		return $this->relationEntity;
	}

	/**
	 * @return ClassMetadata|null
	 */
	public function getRelationMeta(): ?ClassMetadata
	{
		return $this->relationMeta;
	}

	/**
	 * @return array|null
	 */
	public function getRelationMapping(): ?array
	{
		return $this->relationMapping;
	}

	/**
	 * @return string
	 */
	public function getRightEntity(): string
	{
		return $this->rightEntity;
	}

	/**
	 * @return ClassMetadata
	 */
	public function getRightMeta(): ClassMetadata
	{
		return $this->rightMeta;
	}

	/**
	 * @return array|null
	 */
	public function getRightMapping(): ?array
	{
		return $this->rightMapping;
	}

}
