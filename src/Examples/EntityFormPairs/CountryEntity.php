<?php
/**
 * Author: Radek Zíka
 * Email: radek.zika@dipcom.cz
 * Created: 14.12.2018
 */

namespace Examples\EntityFormPairs;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Kdyby\Doctrine\Entities\Attributes\Identifier;
use Doctrine\Common\Collections\Collection;

/**
 * @ORM\Table(name="country")
 * @ORM\Extends
 */
class CountryEntity
{

	use Identifier;

	/**
	 * @var string
	 *
	 * @ORM\Column(name="`name`", type="string", length=50)
	 */
	private $name;

	/**
	 * @return string
	 */
	public function getName(): string
	{
		return $this->name;
	}

	/**
	 * @param string $name
	 * @return $this
	 */
	public function setName(string $name)
	{
		$this->name = $name;
		return $this;
	}

}
