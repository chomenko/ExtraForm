<?php
/**
 * Author: Radek Zíka
 * Email: radek.zika@dipcom.cz
 */

namespace Examples\EntityFormPairsRelation;

use Doctrine\ORM\Mapping as ORM;
use Kdyby\Doctrine\Entities\Attributes\Identifier;

/**
 * @ORM\Table(name="country")
 * @ORM\Extends
 */
class UserCountryEntity
{

	use Identifier;

	/**
	 * @var UserEntity
	 *
	 * @ORM\ManyToOne(targetEntity="Examples\EntityFormPairsRelation\UserEntity", inversedBy="countries")
	 * @ORM\JoinColumn(name="user_id", referencedColumnName="id", nullable=false)
	 */
	private $user;

	/**
	 * @var CountryEntity
	 *
	 * @ORM\ManyToOne(targetEntity="Examples\EntityFormPairsRelation\CountryEntity", inversedBy="users")
	 * @ORM\JoinColumn(name="country_id", referencedColumnName="id", nullable=false)
	 */
	private $country;

	/**
	 * @return UserEntity
	 */
	public function getUser(): UserEntity
	{
		return $this->user;
	}

	/**
	 * @param UserEntity $user
	 * @return $this
	 */
	public function setUser($user)
	{
		$this->user = $user;
		return $this;
	}

	/**
	 * @return CountryEntity
	 */
	public function getCountry(): CountryEntity
	{
		return $this->country;
	}

	/**
	 * @param CountryEntity $country
	 * @return $this
	 */
	public function setCountry(CountryEntity $country)
	{
		$this->country = $country;
		return $this;
	}

}
