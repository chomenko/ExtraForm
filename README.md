# Extra Form

What do you do?
 
- Ability to work with the entity
- Rendering bootstrap style
	- **Grid** allows you to create custom layouts without using templates
	- Input group
- Allows you to use it [symfony/validator](https://github.com/symfony/validator) in entities
- Can be used on relation entity pairs list 

Required:
- nette/di
- nette/forms
- symfony/validator
- kdyby/doctrine

### Install 

````bash
$ composer require chomenko/extra-form
````

### Configure
````neon
extensions:
	ExtraForm: Chomenko\ExtraForm\DI\ExtraFormExtension
````

## Events

Look on this example [Simple event](src/Examples/SimpleEvent). Service tag ``extraForm.events``.

If you do not use [auto-install](https://github.com/chomenko/AutoInstall).

````neon
services:
	SimpleEvent:
		class: Examples\SimpleEvent\Event
		tags: ["modal.factory"]
````

**Form events**
- CRETE_FORM 
- INSTALL_ENTITY
- BEFORE_ADD_COMPONENT
- ADD_COMPONENT
- BEFORE_RENDER
- AFTER_RENDER

**Elements events**
- ATTACHED
- INSTALLED
- SET_OPTION
- ADD_CONSTRAINT
- LOAD_HTTP_DATA
- AFTER_LOAD_HTTP_DATA
- SET_ITEMS
- SET_VALUE
- RENDER

_There are not all things I want now._

## Examples

To create the first form, look at the class [Chomenko\ExtraForm\FormFactory](src/FormFactory.php)

**Entity forms**
- [Simple form](src/Examples/EntityForm)
- [With use symfony assert](src/Examples/EntityFormAssert)
- [Pairs list](src/Examples/EntityFormPairs)
- [Join pairs list](src/Examples/EntityFormPairsJoin)
- [Relation pairs OneToMany ->relation record<- ManyToOne](src/Examples/EntityFormPairsJoin)

**Events**
- [Simple event](src/Examples/SimpleEvent)

## Rendering

Builder preserve the order of the elements, so if you need to edit only two elements, you do not have to edit the whole form

````php
<?php

	//......
	
	/**
	 * @return ExtraForm
	 */
	public function create(): ExtraForm
	{
		$form = $this->createForm();
		
		$form->addText('name', 'Name')
			->setRequired();
		
		$form->addText('surname', 'Surname')
			->setRequired();
		
		$form->addPassword('password', 'Password')
			->setRequired();
		
		$form->addSubmit('send', 'Sign in');
		
		$builder = $form->builder();
		
		$row = $builder->addRow();
		$row->addColMd(6, "name");
		$row->addColMd(6, ["surname"]);
		
		return $form;
	}
	
	//......
	
````







