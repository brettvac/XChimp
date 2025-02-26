<?php
/*****************************************************************
 * @package XChimp
 * @version 2.0
 * @author ThemeXpert
 * @copyright Copyright (C) 2010 - 2011 ThemeXpert. Updates 2025 by Brett Vachon.
 * @license http://www.gnu.org/licenses/gpl-3.0.html GNU/GPLv3 only
 *****************************************************************/
namespace Themexpert\Plugin\User\Xchimp\Field;

// No direct access
defined('_JEXEC') or die;

use Joomla\CMS\Form\Field\ListField;
use Joomla\CMS\Plugin\PluginHelper;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;
use Joomla\Registry\Registry;
use DrewM\MailChimp\MailChimp;

use Joomla\CMS\Factory;

/**
 * This is a form element that displays Mailchimp Lists
 */
class MailchimplistsField extends ListField
{
    /**
     * The form field type.
     *
     * @var    string
     */
    protected $type = 'mailchimplists'; //Not strictly required since we are using Namespaces

    protected function getOptions()
    {
        $plugin = PluginHelper::getPlugin('user', 'xchimp');
        $params = new Registry($plugin->params);

        $options = [];

        if (!empty($params->get('apikey','')))
        {
            // Include Mailchimp API wrapper
            require_once JPATH_PLUGINS . '/user/xchimp/lib/MailChimp.php';
            
            //Create a Mailchimp instance
            $mailchimp = null;
            
            try {
              $mailchimp = new MailChimp($params->get('apikey',''));
              
              //Get all the mailing lists (with a get on the lists method)
              $lists = $mailchimp->get('lists');
            
              if ($lists && !empty($lists['lists'])) {
                  foreach ($lists['lists'] as $list)
                  {
                     $options[] = HTMLHelper::_('select.option', $list['id'], $list['name']);
                  }
                } 
              
              } catch (\Exception $e) {
               $application = Factory::getApplication();
               $application->enqueueMessage($e->getMessage(), 'error');
            }       
        }
        
        else
        {
            $options[] = HTMLHelper::_('select.option', '-1', Text::alt('PROVIDE_API_KEY', preg_replace('/[^a-zA-Z0-9_\-]/', '_', $this->fieldname)));
        }

        // Merge any additional options from the XML definition
        $options = array_merge(parent::getOptions(), $options);

        return $options;
    }
}