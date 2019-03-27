<?php
/**
 * Author: Mykola Chomenko
 * Email: mykola.chomenko@dipcom.cz
 */

namespace Chomenko\ExtraForm\Extend\Pair;

use Chomenko\ExtraForm\EntityForm;
use Chomenko\ExtraForm\Exception\Exception;
use Chomenko\ExtraForm\Extend\EntityExtend;
use Chomenko\ExtraForm\Extend\ExtendValue;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping\ClassMetadata;
use Nette\Forms\Controls\BaseControl;
use Nette\Forms\Controls\MultiChoiceControl;
use Nette\SmartObject;

class Pairs extends EntityExtend implements IPairs
{

	use SmartObject;

	const PATTERN_REGEX = "~{{\s([a-zA-Z_0-9]+)\s}}~";

	/**
	 * @var string
	 */
	protected $entity;

	/**
	 * @var string|null
	 */
	private $pattern;

	/**
	 * @var object[]
	 */
	private $items = [];

	/**
	 * @var bool
	 */
	private $manualSets = FALSE;

	/**
	 * @var PairsRelation
	 */
	private $relation;

	/**
	 * @var string|null
	 */
	private $identifier;

	/**
	 * @var ClassMetadata
	 */
	protected $metadata;

	/**
	 * @var callable
	 */
	private $itemFilter;

	/**
	 * @param string $entity
	 * @param string $patter
	 * @param callable $itemFilter
	 * @param string $identifier
	 */
	public function __construct(string $entity, string $patter = NULL, $itemFilter = NULL, string $identifier = NULL)
	{
		$this->entity = $entity;
		$this->itemFilter = $itemFilter;
		$this->setPattern($patter);
		if ($identifier) {
			$this->seIdentifier($identifier);
		}
	}

	/**
	 * @return string
	 */
	public function getName(): string
	{
		return IPairs::OPTION_KEY;
	}

	/**
	 * @return string
	 */
	public function getEntity(): string
	{
		return $this->entity;
	}

	/**
	 * @param BaseControl $control
	 * @param EntityForm $entityForm
	 * @return mixed|void
	 * @throws Exception
	 */
	public function attached(BaseControl $control, EntityForm $entityForm)
	{
		parent::attached($control, $entityForm);
		$this->metadata = $this->entityManager->getClassMetadata($this->entity);
		$this->relation = new PairsRelation($control->getName(), get_class($entityForm->getEntity()), $this->entity, $this->entityManager);
		$list = $this->create();
		$control->setItems($list);
	}

	/**
	 * @return ClassMetadata
	 * @throws Exception
	 */
	public function getMetadata(): ClassMetadata
	{
		if (!$this->attached) {
			throw Exception::notAttached(get_class($this));
		}
		return $this->metadata;
	}

	/**
	 * @return array
	 * @throws Exception
	 */
	public function create(): array
	{
		$list = [];
		$items = $this->items;
		$pattern = $this->pattern;
		$identifier = $this->getIdentifier();

		if (!$this->manualSets) {
			$items = $this->getItemFromDatabase();
			$this->setItems($items);
		}

		if (!$pattern) {
			$key = $identifier;
			$names = $this->metadata->getFieldNames();

			$nameList = array_keys($names);
			if ($nameList[0] !== $identifier) {
				$key = $names[$nameList[0]];
			} elseif (count($names) > 1 && (is_string($names[$nameList[1]]) || is_numeric($names[$nameList[1]]))) {
				$key = $names[$nameList[1]];
			}
			$pattern = "{{ " . $key . " }}";
		}

		$patternsKeys = $this->getPatternsKeys($pattern);

		foreach ($items as $item) {


			if ($this->itemFilter) {
				$result = call_user_func($this->itemFilter, $item);
				if (!is_object($result) || !$item instanceof $result) {
					continue;
				}
			}

			$labelItem = $pattern;
			$origin = $this->getForm()->getUnitOfWork()->getOriginalEntityData($item);

			if (!array_key_exists($identifier, $origin)) {
				throw Exception::emptyIdentifier(get_class($item));
			}

			foreach ($patternsKeys as $i => $key) {
				if (!array_key_exists($key["name"], $origin)) {
					throw Exception::doesNotExistField($key["name"], get_class($item));
				}

				$value = $origin[$key["name"]];
				if (!(is_string($value) || is_numeric($value))) {
					throw Exception::fieldDoesNotString($key["name"], get_class($item));
				}
				$patternsKeys[$i]["translate"] = $value;
			}

			foreach ($patternsKeys as $key) {
				$labelItem = str_replace($key["search"], $key["translate"], $labelItem);
			}
			$list[$origin[$identifier]] = $labelItem;
		}
		return $list;
	}

	/**
	 * @param array|Collection $items
	 * @internal
	 * @throws Exception
	 */
	public function setSelected(&$items)
	{
		if (!$this->attached) {
			throw Exception::notAttached(get_class($this));
		}

		if (!(is_array($items) || (is_object($items)) && get_class($this) !== $this->entity)) {
			return;
		}

		$identifier = $this->getIdentifier();
		if ($this->relation->hasMediator()) {
			$identifier = $this->relation->getRightRelationColumnName();
		}

		if ($this->control instanceof MultiChoiceControl) {

			if ($items instanceof Collection) {
				if ($items->isEmpty()) {
					$items = [];
					return;
				}
			} elseif (!is_object(end($items->getValues()))) {
				return;
			}

			$values = [];
			foreach ($items as $item) {
				$data = $this->entityManager->getUnitOfWork()->getOriginalEntityData($item);
				if (array_key_exists($identifier, $data)) {
					$values[] = $data[$identifier];
				}
			}
			$items = $values;
			return;
		}

		$data = $this->entityManager->getUnitOfWork()->getOriginalEntityData($items);
		if (array_key_exists($identifier, $data)) {
			$items = $data[$identifier];
		}
	}

	/**
	 * @param object $entity
	 * @param ExtendValue $value
	 * @return mixed|void
	 * @throws Exception
	 */
	public function executeData(object $entity, ExtendValue $value)
	{
		parent::executeData($entity, $value);

		if (!$this->relation->hasRelation()) {
			return;
		}

		$selected = $this->getItemsByControlVale();
		$value->setRelationType($this->relation->hasRelation());
		$name = $this->control->getName();

		if (is_array($selected)) {

			$columns = $this->form->getMetaData()->getFieldValue($entity, $name);
			if (!$columns instanceof Collection) {
				throw Exception::fieldMustCollection($name);
			}

			$field = $this->relation->getRelationFieldName();
			$rightField = $this->relation->getRightRelationFieldName();

			$addList = [];
			foreach ($selected as $selectEntity) {

				if ($this->containsRelation($columns, $entity, $selectEntity)) {
					continue;
				}

				$relationEntity = $this->form->createEntityInstance($this->relation->getRelationEntity());
				$entityMeta = $this->entityManager->getClassMetadata($this->relation->getRelationEntity());
				$entityMeta->setFieldValue($relationEntity, $field, $entity);
				$entityMeta->setFieldValue($relationEntity, $rightField, $selectEntity);

				$addList[] = $relationEntity;
			}

			$collection = new ArrayCollection($selected);

			foreach ($columns as $defaultValue) {

				if ($this->containsRelation($collection, $entity, $defaultValue, TRUE) || $collection->isEmpty()) {
					continue;
				}
				$this->entityManager->remove($defaultValue);
				$columns->removeElement($defaultValue);
			}

			foreach ($addList as $item) {
				$this->entityManager->persist($item);
				$columns->add($item);
			}
			$value->setNewValue($columns);
			return;
		}

		$value->setNewValue($this->getItem($value->getOriginValue()));
	}

	/**
	 * @param Collection $collection
	 * @param object $entity
	 * @param object $diffEntity
	 * @param bool $right
	 * @return bool
	 */
	protected function containsRelation(Collection $collection, object $entity, object $diffEntity, bool $right = FALSE): bool
	{
		$unitOfWork = $this->entityManager->getUnitOfWork();
		$entityData = $unitOfWork->getOriginalEntityData($entity);
		$diffData = $unitOfWork->getOriginalEntityData($diffEntity);

		$entityRelationColumn = $this->relation->getEntityRelationColumnName();
		$rightRelationColumn = $this->relation->getRightRelationColumnName();
		$entityColumn = $this->relation->getEntityColumnName();
		$rightColumn = $this->relation->getRightColumnName();

		foreach ($collection as $column) {
			$columnData = $unitOfWork->getOriginalEntityData($column);
			if ($right) {
				if ($diffData[$entityRelationColumn] === $entityData[$entityColumn] && $diffData[$rightRelationColumn] === $columnData[$rightColumn]) {
					return TRUE;
				}
				continue;
			}
			if ($columnData[$entityRelationColumn] === $entityData[$entityColumn] && $columnData[$rightRelationColumn] === $diffData[$rightColumn]) {
				return TRUE;
			}
		}
		return FALSE;
	}

	/**
	 * @param string $template
	 * @return array
	 */
	protected function getPatternsKeys(string $template): array
	{
		$keys = [];
		preg_match_all(self::PATTERN_REGEX, $template, $matches);
		foreach ($matches[0] as $index => $value) {
			$name = $matches[1][$index];
			$keys[$name] = [
				"search" => $value,
				"name" => $name,
				"translate" => "",
			];
		}
		return $keys;
	}

	/**
	 * @return array
	 * @throws Exception
	 */
	protected function getItemFromDatabase()
	{
		$manager = $this->getForm()->getEntityManager();
		$repo = $manager->getRepository($this->entity);
		return $repo->findAll();
	}

	/**
	 * @param mixed $value
	 * @return mixed|object
	 * @throws Exception
	 */
	public function getItem($value)
	{
		$identifier = $this->getIdentifier();
		foreach ($this->getItems() as $item) {
			if ($this->getMetadata()->getFieldValue($item, $identifier) === $value) {
				return $item;
			}
		}
	}

	/**
	 * @param array $items
	 */
	public function setItems(array $items)
	{
		$this->manualSets = TRUE;
		$this->items = $items;
	}

	/**
	 * @param string $identifier
	 */
	public function seIdentifier(string $identifier)
	{
		$this->identifier = $identifier;
	}

	/**
	 * @return string
	 * @throws Exception
	 */
	public function getIdentifier(): string
	{
		if ($this->identifier) {
			return $this->identifier;
		}

		$fields = $this->getMetadata()->getIdentifierFieldNames();
		if (!isset($fields[0])) {
			throw Exception::noIdentifier($this->entity);
		}
		return $fields[0];
	}

	/**
	 * @return array|object|null
	 * @throws Exception
	 */
	private function getItemsByControlVale()
	{
		$value = $this->control->getValue();
		$result = NULL;
		if (is_array($value)) {
			$items = [];
			foreach ($value as $key) {
				$items[] = $this->getItem($key);
			}
			return $items;
		}
		return $this->getItem($value);
	}

	/**
	 * @return object[]
	 * @throws Exception
	 */
	public function getItems(): array
	{
		if (!$this->attached) {
			throw Exception::notAttached(get_class($this));
		}
		return $this->items;
	}

	/**
	 * @param string $pattern
	 * @return $this
	 */
	public function setPattern(string $pattern = NULL)
	{
		$this->pattern = $pattern;
		return $this;
	}

}
