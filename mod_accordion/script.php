<?php
/**
 * @package    JJ_Accordion
 * @author     JoomJunk <admin@joomjunk.co.uk>
 * @copyright  Copyright (C) 2011 - 2014 JoomJunk. All Rights Reserved
 * @license    GNU General Public License version 3; http://www.gnu.org/licenses/gpl-3.0.html
 */

// No direct access to this file
defined('_JEXEC') or die('Restricted access');

/**
 * Accordion installation script class.
 *
 * @since  3.0.0
 */
class Mod_AccordionInstallerScript
{
	/**
	 * @var		string	The version number of the module.
	 * @since   3.0.0
	 */
	protected $release = '';

	/**
	 * @var		string	The table the parameters are stored in.
	 * @since   3.0.0
	 */
	protected $paramTable = '#__modules';

	/**
	 * @var		string	The extension name.
	 * @since   3.0.0
	 */
	protected $extension = 'mod_accordion';

	/**
	 * Function called before module installation/update/removal procedure commences
	 *
	 * @param   string                   $type    The type of change (install, update or discover_install
	 *                                            , not uninstall)
	 * @param   JInstallerAdapterModule  $parent  The class calling this method
	 *
	 * @return  void
	 *
	 * @since  3.0.0
	 */
	public function preflight($type, $parent)
	{
		// Module manifest file version
		$this->release = $parent->get("manifest")->version;

		// Abort if the module being installed is not newer than the currently installed version
		if ($type == 'Update')
		{
			$manifest = $this->getItemArray('manifest_cache', '#__extensions', 'element', JFactory::getDbo()->quote($this->extension));
			$oldRelease = $manifest['version'];

			if (version_compare($oldRelease, $this->release, '<'))
			{
				// Update to reflect move from assets subfolder to media folder
				if (version_compare($oldRelease, '3.0.0', '<='))
				{
					$this->update300();
				}

				// Update to reflect remove the js file
				if (version_compare($oldRelease, '3.0.2', '<='))
				{
					$this->update302();
				}

				// Update to reflect that fact we still shipped the assets file after 3.0.0 - oops!
				if (version_compare($oldRelease, '3.2.0', '<='))
				{
					$this->update320();
				}
			}
		}
	}

	/**
	 * Function called on install of module
	 *
	 * @param   JInstallerAdapterModule  $parent  The class calling this method
	 *
	 * @return  void
	 *
	 * @since  3.0.0
	 */
	public function install($parent)
	{
		echo '<p>' . JText::_('JJACCORDIONDESC') . '</p>';
	}

	/**
	 * Function called on update of module
	 *
	 * @param   JInstallerAdapterModule  $parent  The class calling this method
	 *
	 * @return  void
	 *
	 * @since 3.0.0
	 */
	public function update($parent)
	{
		echo '<p>' . JText::_('JJACCORDIONDESC') . '</p>';
	}


	/**
	 * Function to update the file structure for the Social Slider Version 1.4.0 updates
	 *
	 * @return  void
	 *
	 * @since  3.0.0
	 */
	protected function update300()
	{
		// Import dependencies
		jimport('joomla.filesystem.folder');
		jimport('joomla.filesystem.file');

		// Move the assets and add index.html files to new directory
		if (JFolder::create('media/' . $this->extension)
			&& JFile::move(JUri::root() . 'modules/'. $this->extension . '/assets/accordion.css', JUri::root() . 'media/'. $this->extension . '/css/accordion.css')
			&& JFile::move(JUri::root() . 'modules/'. $this->extension . '/assets/accordion-bootstrap.css', JUri::root() . 'media/'. $this->extension . '/css/accordion-bootstrap.css')
			&& JFile::move(JUri::root() . 'modules/'. $this->extension . '/assets/accordion-dark.css', JUri::root() . 'media/'. $this->extension . '/accordion-dark.css')
			&& JFile::move(JUri::root() . 'modules/'. $this->extension . '/assets/accordion-light.css', JUri::root() . 'media/'. $this->extension . '/accordion-light.css'))
		{
			// We can now delete the folder
			JFolder::delete(JPATH_ROOT . '/modules/'. $this->extension . '/assets');
		}
	}

	/**
	 * Remove redundant js file.
	 *
	 * @return  void
	 *
	 * @since  3.0.2
	 */
	protected function update302()
	{
		// Import dependencies
		jimport('joomla.filesystem.file');

		// We can now delete the folder
		JFile::delete(JPATH_ROOT . '/media/'. $this->extension . '/js/accordion.js');
	}

	/**
	 * We never removed the shipped assets directory in 3.0.0 - smooth!
	 *
	 * @return  void
	 *
	 * @since  3.0.2
	 */
	protected function update320()
	{
		// Import dependencies
		jimport('joomla.filesystem.folder');

		// Delete the assets dir properly this time
		JFolder::delete(JPATH_ROOT . '/modules/'. $this->extension . '/assets');
	}

	/**
	 * Builds a standard select query to produce better DRY code in this script.
	 * This should produce a single unique cell which is json encoded
	 *
	 * @param   string  $element     The element to get from the query
	 * @param   string  $table       The table to search for the data in
	 * @param   string  $column      The column of the database to search from
	 * @param   mixed   $identifier  The integer id or the already quoted string
	 *
	 * @return  array  associated array containing data from the cell
	 *
	 * @since 3.0.2
	 */
	protected function getItemArray($element, $table, $column, $identifier)
	{
		// Get the DB and query objects
		$db = JFactory::getDbo();
		$query = $db->getQuery(true);

		// Build the query
		$query->select($db->quoteName($element))
			->from($db->quoteName($table))
			->where($db->quoteName($column) . ' = ' . $identifier);
		$db->setQuery($query);

		// Load the single cell and json_decode data
		$array = json_decode($db->loadResult(), true);

		return $array;
	}
}
