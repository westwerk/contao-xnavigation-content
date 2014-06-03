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

namespace Bit3\Contao\XNavigation\Content\Provider;

use Bit3\FlexiTree\Event\CollectItemsEvent;
use Bit3\FlexiTree\Event\CreateItemEvent;
use Contao\PageModel;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

/**
 * Class ContentProvider
 */
class ContentProvider implements EventSubscriberInterface
{
	/**
	 * {@inheritdoc}
	 */
	public static function getSubscribedEvents()
	{
		return array(
			'create-item'   => 'createItem',
			'collect-items' => array('collectItems', 100),
		);
	}

	public function collectItems(CollectItemsEvent $event)
	{
		$item = $event->getParentItem();

		if ($item->getType() == 'article') {
			$t          = \ContentModel::getTable();
			$arrColumns = array("$t.pid=?", "($t.ptable='' OR $t.ptable='tl_article')", "$t.cssID!=''");

			if (!BE_USER_LOGGED_IN) {
				$time         = time();
				$arrColumns[] = "($t.start='' OR $t.start<$time) AND ($t.stop='' OR $t.stop>$time) AND $t.invisible=''";
			}

			$contents = \ContentModel::findBy(
				$arrColumns,
				array($item->getName()),
				array('order' => 'sorting')
			);

			if ($contents) {
				$factory = $event->getFactory();

				$reachedLevel = 7;

				foreach ($contents as $content) {
					$headline = deserialize($content->headline, true);
					$cssID    = deserialize($content->cssID, true);

					if (
						!empty($headline['value']) &&
						!empty($headline['unit']) &&
						!empty($cssID[0])
					) {
						$elementLevel = (int) substr($headline['unit'], 1);

						if ($elementLevel <= $reachedLevel) {
							$factory->createItem('content', $content->id, $item);

							$reachedLevel = $elementLevel;
						}
					}
				}
			}
		}

		else if ($item->getType() == 'content') {
			$thisHeadline = deserialize($item->getExtra('headline'), true);

			if (!empty($thisHeadline['unit'])) {
				$expectedLevel = intval(substr($thisHeadline['unit'], 1)) + 1;

				$t          = \ContentModel::getTable();
				$arrColumns = array(
					"$t.pid=?",
					"($t.ptable='' OR $t.ptable='tl_article')",
					"$t.cssID!=''",
					"$t.sorting > ?"
				);

				if (!BE_USER_LOGGED_IN) {
					$time         = time();
					$arrColumns[] = "($t.start='' OR $t.start<$time) AND ($t.stop='' OR $t.stop>$time) AND $t.invisible=''";
				}

				$contents = \ContentModel::findBy(
					$arrColumns,
					array($item->getExtra('pid'), $item->getExtra('sorting')),
					array('order' => 'sorting')
				);

				if ($contents) {
					$factory = $event->getFactory();

					$reachedLevel = 7;

					foreach ($contents as $content) {
						$headline = deserialize($content->headline, true);
						$cssID    = deserialize($content->cssID, true);

						if (
							!empty($headline['value']) &&
							!empty($headline['unit']) &&
							!empty($cssID[0])
						) {
							$elementLevel = (int) substr($headline['unit'], 1);

							// level is one down
							if ($elementLevel >= $expectedLevel && $elementLevel <= $reachedLevel) {
								$factory->createItem('content', $content->id, $item);

								$reachedLevel = $elementLevel;
							}
							// level goes up
							else if ($elementLevel < $expectedLevel) {
								break;
							}
						}
					}
				}
			}
		}
	}

	public function createItem(CreateItemEvent $event)
	{
		$item = $event->getItem();

		if ($item->getType() == 'content') {
			$content = \ContentModel::findByPk($item->getName());

			if ($content && ($content->ptable == '' || $content->ptable == 'tl_article')) {
				$article = \ArticleModel::findByPk($content->pid);

				if ($article) {
					$page = \PageModel::findByPk($article->pid);

					if ($page) {
						$headline = deserialize($content->headline, true);
						$cssID    = deserialize($content->cssID, true);

						if (
							!empty($headline['value']) &&
							!empty($headline['unit']) &&
							!empty($cssID[0])
						) {
							$item->setUri(\Frontend::generateFrontendUrl($page->row()) . '#' . $cssID[0]);
							$item->setLabel($headline['value']);

							$item->setExtras($content->row());
						}
					}
				}
			}
		}
	}

	/**
	 * @SuppressWarnings(PHPMD.Superglobals)
	 * @SuppressWarnings(PHPMD.CamelCaseVariableName)
	 * @return \PageModel
	 */
	protected function getCurrentPage()
	{
		return $GLOBALS['objPage'];
	}
}
