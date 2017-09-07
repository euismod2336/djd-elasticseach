<?php
/**
 * @package     DJD.ElasticSearch
 *
 * @copyright   Copyright (C) 2005 - 2017 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

/**
 * DJD Elastic Search Plugin
 *
 * @since  0.1
 */
class PlgContentDJDES extends JPlugin
{
	/**
	 * Event method that is run after an item is saved
	 *
	 * @param   string  $context The context of the content
	 * @param   object  $item    A JTableContent object
	 * @param   boolean $isNew   If the content is just about to be created
	 *
	 * @return    boolean  Return value
	 */
	public function onContentAfterSave($context, $item, $isNew)
	{
		$result = true;

		if ($context == 'com_content.article' || $context == 'com_content.form')
		{
			JLoader::register('ContentHelperRoute', JPATH_SITE . '/components/com_content/helpers/route.php');

			// Just basic title + description is all we are saving for now
			$data = array(
				'id'          => $item->id,
				// Name is required field for ES, so we map the title onto it
				'name'        => $item->title,
				'description' => JHtml::_('string.truncate', $item->introtext . $item->fulltext, 150),
				'fullbody'    => $item->introtext . $item->fulltext,
				'path'        => JRoute::_(ContentHelperRoute::getArticleRoute($item->id, $item->catid)),
				'catid'       => $item->catid,
				'state'       => $item->state,
				'access'      => $item->access
			);

			// Now we can store this item
			$this->call($item->id, 'put', $data);
		}

		$this->onGetFromElasticSearch('tekst');

		return $result;
	}

	/**
	 * Simple event call that searches ElasticSearch for a given value
	 * Searching is actually very complex, depending on your needs, but this is a simplyfied example.
	 * See: https://www.elastic.co/guide/en/elasticsearch/reference/5.5/search.html
	 *
	 * @param   string $value The value to search for
	 *
	 * @return  array Array of found objects
	 *
	 * @since 0.1
	 */
	public function onGetFromElasticSearch($value)
	{
		$result = $this->call('_search?q=' . JFilterOutput::stringURLSafe($value));
		$result = json_decode($result->body);

		return isset($result->hits) ? $result->hits->hits : array();
	}

	/**
	 * Call an url via JHttpFactory.
	 *
	 * For our children and our childrens children, the implementation of this function is not safe and should not be used outside
	 * of this context :p
	 *
	 * @param   string $url  The total url to call
	 * @param   string $type Optional type of the call
	 * @param   array  $data Optional data to send with the call
	 *
	 * @return JHttpResponse
	 *
	 * @since 0.1
	 */
	private function call($url, $type = 'get', $data = array())
	{
		$endpoint = $this->params->get('es_host', '127.0.0.1:9200');

		$url = implode('/',
			array(
				$endpoint,
				$this->params->get('es_index', 'djd'),
				// Assume we only store 1 type of content
				'content',
				$url
			)
		);

		$headers = array(
			'Authorization' => 'Basic ' . base64_encode($this->params->get('es_user', 'elastic') . ':' . $this->params->get('es_pass', 'changeme')),
			'Content-Type'  => 'application/json'
		);

		// If we have data to send, the function has a different signature
		if (count($data) > 0)
		{
			return JHttpFactory::getHttp()->{$type}($url, json_encode($data), $headers);
		}
		else
		{
			return JHttpFactory::getHttp()->{$type}($url, $headers);
		}
	}

	/**
	 * This is not actually required but allows you to tell ElasticSearch how to index/search the content allowing for much more flexibility.
	 * See: https://www.elastic.co/guide/en/elasticsearch/reference/5.5/indices-create-index.html
	 * And: https://www.elastic.co/guide/en/elasticsearch/reference/5.5/indices-put-mapping.html
	 *
	 * @return void
	 *
	 * @since 0.1
	 */
	private function createIndex()
	{
		// Calling head gives result on whether the index exists or not, 200 means yes, 404 means no :)
		$oExists = $this->call('', 'head');

		if ($oExists->code !== 200)
		{
			// This is pseudo-code, not tested
			$data = array(
				'settings' => array(
					'number_of_shards'   => 1,
					'number_of_replicas' => 1
				),
				'mappings' => array(
					'content' => array(
						'properties' => array(
							'name'        => array(
								'type' => 'text'
							),
							'description' => array(
								'type' => 'text'
							)
						)
					)
				)
			);

			$this->call('', 'put', $data);
		}
	}
}
