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
 * Class ContentPublishedCondition
 */
class ContentPublishedCondition implements ConditionInterface
{
    /**
	 * {@inheritdoc}
	 */
    public function matchItem(ItemInterface $item)
    {
        if ($item->getType() != 'content') {
            return true;
        }

        $invisible = $item->getExtra('invisible');
        $start     = $item->getExtra('start');
        $stop      = $item->getExtra('stop');
        $time      = time();

        return !$invisible && (!$start || $start <= $time) && (!$stop || $stop >= $time);
    }

    /**
	 * {@inheritdoc}
	 */
    public function describe()
    {
        return 'content.published';
    }
}
