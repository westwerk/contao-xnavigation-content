<?php if (!defined('TL_ROOT')) die('You can not access this file directly!');

/**
 * Contao Open Source CMS
 * Copyright (C) 2005-2010 Leo Feyer
 *
 * Formerly known as TYPOlight Open Source CMS.
 *
 * This program is free software: you can redistribute it and/or
 * modify it under the terms of the GNU Lesser General Public
 * License as published by the Free Software Foundation, either
 * version 3 of the License, or (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU
 * Lesser General Public License for more details.
 *
 * You should have received a copy of the GNU Lesser General Public
 * License along with this program. If not, please visit the Free
 * Software Foundation website at <http://www.gnu.org/licenses/>.
 *
 * PHP version 5
 * @copyright  InfinitySoft 2010
 * @author     Tristan Lins <tristan.lins@infinitysoft.de>
 * @package    xNavigation - Content
 * @license    http://opensource.org/licenses/lgpl-3.0.html
 */


class xNavigationContentProvider extends xNavigationProvider
{
	public function generateItems(DC_Table $objCurrentPage, $blnActive, &$arrItems, $arrGroups, $intLevel, $intMaxLevel) 
	{
		
		// Get article navigation
		if (	$objCurrentPage->id > 0
			&& (	$objCurrentPage->xNavigationIncludeArticles == 'map_always'
				||  (	$this instanceof ModuleXSitemap
					||  $blnActive)
				&&  $objCurrentPage->xNavigationIncludeArticles == 'map_active'))
		{
			$stmtArticles = $this->Database->prepare("
				SELECT
					id
				FROM
					tl_article
				WHERE
						pid = ?
					AND xNavigation != 'map_never'
					AND inColumn = 'main'"
					. ((FE_USER_LOGGED_IN && !BE_USER_LOGGED_IN && !$this->showProtected) ? " AND guests!=1" : "")
					. (!BE_USER_LOGGED_IN ? " AND (start='' OR start<".$time.") AND (stop='' OR stop>".$time.") AND published=1" : ""));
			$objArticles = $stmtArticles->execute($objCurrentPageID);
			
			// Render article navigation
			if ($objArticles) {
				$this->import('ArticleNavigation');
				$articleIDs = array();
				while($objArticles->next()) {
					$articleIDs[] = $objArticles->id;
				}
				$arrItems = array_merge($arrItems,
					$this->convertArticles2Navigation($objTemplate, $this->ArticleNavigation->fromArticles($articleIDs), $level));
			}
			
		}
	}
	
	/**
	 * Converts the articles array structure into a 1 dimension flat structure, that only contains the top level.
	 * All subitems are converted and parsed by the navigation template.
	 * 
	 * @param Database_Result FrontendTemplate
	 * @param mixed $articles
	 * @param integer $level
	 * @param boolean $toString
	 * @return array
	 */
	protected function convertArticles2Navigation(FrontendTemplate &$objTemplate, $articles, $level, $toString = false) {
		foreach ($articles as &$article) {
			// Set the item link
			$article['link'] = $article['title'];
			// Flatten subitems
			if (isset($article['subitems'])) {
				$article['subitems'] = $this->convertArticles2Navigation($objTemplate, $article['subitems'], $level + 1, true);
			}
		}
		
		// Add classes first and last
		if (count($articles))
		{
			$last = count($articles) - 1;

			$articles[0]['class'] = trim($articles[0]['class'] . ' first_article' . ($toString ? ' first' : ''));
			$articles[$last]['class'] = trim($articles[$last]['class'] . ' last_article' . ($toString ? ' last' : ''));
		}
		
		// Parse by template or return the modified array
		if ($toString) {
			$objTemplate->level = 'level_' . $level++;
			$objTemplate->items = $articles;
			return $objTemplate->parse();
		} else {
			return $articles;
		}
	}
	
}
?>