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
 * Class ContentProtectedCondition
 */
class ContentProtectedCondition implements ConditionInterface
{

    /**
	 * @var bool
	 */
    protected $acceptedProtectedStatus;

    public function __construct($acceptedProtectedStatus = false)
    {
        $this->acceptedProtectedStatus = $acceptedProtectedStatus;
    }

    /**
	 * @param boolean $acceptedProtectedStatus
	 */
    public function setAcceptedProtectedStatus($acceptedProtectedStatus)
    {
        $this->acceptedProtectedStatus = $acceptedProtectedStatus;

        return $this;
    }

    /**
	 * @SuppressWarnings(PHPMD.BooleanGetMethodName)
	 * @return boolean
	 */
    public function getAcceptedProtectedStatus()
    {
        return $this->acceptedProtectedStatus;
    }

    /**
	 * {@inheritdoc}
	 */
    public function matchItem(ItemInterface $item)
    {
        if ($item->getType() != 'content') {
            return true;
        }

        $protected = $item->getExtra('protected');

        return $protected == $this->acceptedProtectedStatus;
    }

    /**
	 * {@inheritdoc}
	 */
    public function describe()
    {
        return $this->acceptedProtectedStatus ? 'content.protected' : '!content.protected';
    }
}
