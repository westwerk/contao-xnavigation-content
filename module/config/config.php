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

/**
 * Event listener
 */
$GLOBALS['TL_EVENT_SUBSCRIBERS'][] = 'Bit3\Contao\XNavigation\Content\DefaultSubscriber';

/**
 * Provider
 */
$GLOBALS['XNAVIGATION_PROVIDER']['content'] = 'Bit3\Contao\XNavigation\Content\Provider\ContentProvider';

/**
 * Conditions
 */
$GLOBALS['XNAVIGATION_CONDITION']['content_guests']    = 'Bit3\Contao\XNavigation\Content\Condition\ContentGuestsCondition';
$GLOBALS['XNAVIGATION_CONDITION']['content_protected'] = 'Bit3\Contao\XNavigation\Content\Condition\ContentProtectedCondition';
$GLOBALS['XNAVIGATION_CONDITION']['content_groups']    = 'Bit3\Contao\XNavigation\Content\Condition\ContentGroupsCondition';
$GLOBALS['XNAVIGATION_CONDITION']['content_published'] = 'Bit3\Contao\XNavigation\Content\Condition\ContentPublishedCondition';
