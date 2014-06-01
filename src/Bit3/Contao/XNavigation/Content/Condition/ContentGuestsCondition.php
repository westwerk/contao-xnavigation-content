<?php

/**
 * xNavigation - Highly extendable and flexible navigation module for the Contao Open Source CMS
 *
 * Copyright (C) 2013 bit3 UG <http://bit3.de>
 *
 * @package    xNavigation
 * @author     Tristan Lins <tristan.lins@bit3.de>
 * @link       http://www.themeplus.de
 * @license    http://www.gnu.org/licenses/lgpl-3.0.html LGPL
 */

namespace Bit3\Contao\XNavigation\Content\Condition;

use Bit3\FlexiTree\Condition\ConditionInterface;
use Bit3\FlexiTree\ItemInterface;

/**
 * Class ContentGuestsCondition
 */
class ContentGuestsCondition implements ConditionInterface
{

	/**
	 * @var bool
	 */
	protected $acceptedGuestsStatus;

	public function __construct($acceptedGuestsStatus = false)
	{
		$this->setAcceptedGuestsStatus($acceptedGuestsStatus);
	}

	/**
	 * @param boolean $acceptedGuestsStatus
	 */
	public function setAcceptedGuestsStatus($acceptedGuestsStatus)
	{
		$this->acceptedGuestsStatus = (bool) $acceptedGuestsStatus;
		return $this;
	}

	/**
	 * @SuppressWarnings(PHPMD.BooleanGetMethodName)
	 * @return boolean
	 */
	public function getAcceptedGuestsStatus()
	{
		return $this->acceptedGuestsStatus;
	}

	/**
	 * {@inheritdoc}
	 */
	public function matchItem(ItemInterface $item)
	{
		if ($item->getType() != 'content') {
			return true;
		}

		$guests = $item->getExtra('guests');
		return $guests == $this->acceptedGuestsStatus;
	}

	/**
	 * {@inheritdoc}
	 */
	public function describe()
	{
		return $this->acceptedGuestsStatus ? 'content.guestsOnly' : '!content.guestsOnly';
	}
}
