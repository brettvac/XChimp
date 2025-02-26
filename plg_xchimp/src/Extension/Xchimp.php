<?php
/*****************************************************************
 * @package XChimp
 * @version 2.0
 * @author ThemeXpert
 * @copyright Copyright (C) 2010 - 2011 ThemeXpert. Updates 2025 by Brett.
 * @license http://www.gnu.org/licenses/gpl-3.0.html GNU/GPLv3 only
 *****************************************************************/
namespace Themexpert\Plugin\User\Xchimp\Extension;

use Joomla\CMS\Plugin\CMSPlugin;
use Joomla\Event\SubscriberInterface;
use Joomla\Event\Event;

use DrewM\MailChimp\MailChimp;

defined('_JEXEC') or die; // No direct access

/**
 * Xchimp User Plugin
 *
 * @since  1.6
 */
class Xchimp extends CMSPlugin implements SubscriberInterface
{
  public static function getSubscribedEvents(): array
  {
    return [
        'onUserAfterSave' => 'handleUserAfterSave'
    ];
  }
    /**
     * Method is called after user data is stored in the database
     *
     * @param   array    $user     Holds the new user data
     * @param   boolean  $isnew    True if a new user is stored
     * @param   boolean  $success  True if user was successfully stored
     * @param   string   $msg      Message
     *
     * @return  void
     * @throws  Exception
     */
    public function handleUserAfterSave(Event $event): void
    {
      [ $user, $isnew, $success, $msg ] = array_values($event->getArguments());       

         if (!$success) {
            return;
        }

        $apikey = $this->params->get('apikey');
        $listid = $this->params->get('listid');
        $doubleOptin = $this->params->get('double_optin') ? true : false;
        $welcomeEmail = $this->params->get('welcome_email') ? true : false;
        $tagsInput = $this->params->get('tags');

        // Split the name for first and last name
        $name = explode(' ', trim($user['name']));
        $firstName = $name[0] ?? '';
        $lastName = $name[1] ?? '';
        
        //Create array from tags for Mailchimp subscription
        $tags = [];
        if (!empty($tagsInput)) 
         {
         $tagsArray = explode(',', $tagsInput);
    
         foreach ($tagsArray as $tag) 
           {
           $tag = trim($tag);
           if ($tag !== '') 
             { 
             $tags[] = $tag; // Just push the tag as a string
             }   
           }
         } 
        
        // Manually include the MailChimp library
        require_once JPATH_PLUGINS . '/user/xchimp/lib/MailChimp.php';
        $mailchimp = new MailChimp($apikey);

        //Subscribe the user. TODO: Log the return message from the Mailchimp API
        $mailchimp->post("lists/$listid/members", [
            'email_address' => $user['email'],
            'status' => $doubleOptin ? 'pending' : 'subscribed',
            'merge_fields' => ['FNAME' => $firstName, 'LNAME' => $lastName],
            'tags' => $tags,
        ]); 
    }
}